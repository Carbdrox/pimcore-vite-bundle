import fs from 'fs';
import path from 'path';

function colorize(text, r, g, b, isBold = false) {
    return `\x1b[38;2;${r};${g};${b}m${isBold ? '\x1b[1m' : ''}${text}\x1b[0m`
}

function getServerUrl(config) {
    const protocol = config.server.https ? 'https' : 'http';

    const hmrHost = typeof config.server.hmr === 'object' ? config.server.hmr.host : null;
    const host = hmrHost ?? config.server.host;

    const hmrPort = typeof config.server.hmr === 'object' ? config.server.hmr.clientPort : null;
    const port = hmrPort ?? config.server.port;

    return `${protocol}://${host}:${port}`
}

function getPimcoreVersion() {
    try {
        const lockData = fs.readFileSync('composer.lock');

        return JSON.parse(lockData.toString())
            .packages?.find(cPackage => cPackage.name === 'pimcore/pimcore')?.version ?? ''
    }
    catch {
        return '';
    }
}

function getPluginVersion() {
    try {
        const lockData = fs.readFileSync('composer.lock');

        return JSON.parse(lockData.toString())
            .packages?.find(cPackage => cPackage.name === 'carbdrox/pimcore-vite-bundle')?.version ?? ''
    }
    catch {
        return ''
    }
}

export default function pimcoreVitePlugin() {

    let installedProcessEndHandlers = false;
    const serveFile = path.join('public', 'vite-serve');
    const colors = {
        'purple': [100,40,180],
        'cyan': [44, 181, 233],
        'grey': [128, 128, 128]
    }

    const removeServeFile = () => {
        if (fs.existsSync(serveFile)) {
            fs.rmSync(serveFile)
        }
    }

    return {
        name: 'vite-pimcore',
        enforce: 'post',
        configureServer(server) {

            if (!installedProcessEndHandlers) {
                process.on('SIGTERM', process.exit);
                process.on('SIGHUP', process.exit);
                process.on('SIGINT', process.exit);
                process.on('exit', removeServeFile);

                installedProcessEndHandlers = true;
            }

            server.httpServer?.once('listening', () => {

                const serverUrl = getServerUrl(server.config);
                fs.writeFileSync(serveFile, serverUrl);

                const portResult = serverUrl.match(/:(\d+)/);
                const port = portResult.length > 1 ? portResult[1] : '';
                const url = port ? serverUrl.replace(portResult[0], '') : serverUrl;

                //timeout needed, so that the Log is written at the end..
                setTimeout(() => {
                    server.config.logger.info(`\n  ${colorize('PIMCORE', ...colors['purple'], true)}`
                        + ` ${colorize(getPimcoreVersion(), ...colors['purple'])}`
                        + ` ${colorize('plugin', ...colors['grey'])}`
                        + ` ${colorize(getPluginVersion(), ...colors['grey'], true)}`);
                    server.config.logger.info('');
                    server.config.logger.info(`  ${colorize('➜', ...colors['purple'])} `
                        + ` ${colorize('Local', ...colors['grey'], true)}:`
                        + `   ${colorize(url, ...colors['cyan'])}`);
                }, 100)
            });

            return () => server.middlewares.use((req, res, next) => {
                next();
            });
        }
    }
}
