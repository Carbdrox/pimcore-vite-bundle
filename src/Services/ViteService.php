<?php declare(strict_types=1);

namespace Carbdrox\Pimcore\ViteBundle\Services;

class ViteService
{
    private string $env;
    private string $appUrl;
    private int $vitePort;
    private bool $viteSecure;
    private string $projectDirectory;
    private mixed $viteManifest = null;

    public function __construct(string $env, string $appUrl, string $projectDirectory, int $vitePort, bool $viteSecure)
    {
        $this->env = $env;
        $this->appUrl = $appUrl;
        $this->vitePort = $vitePort;
        $this->viteSecure = $viteSecure;
        $this->projectDirectory = $projectDirectory;
    }

    public function hasHotReload(): bool
    {
        return $this->env === 'dev' && file_exists($this->projectDirectory . '/public/vite-serve');
    }

    public function getViteAsset(string $assetPath): string
    {
        if ($this->hasHotReload()) {
            return sprintf(
                '%s://%s:%u/%s',
                $this->viteSecure ? 'https' : 'http',
                $this->appUrl,
                $this->vitePort,
                $assetPath
            );
        }

        if (!$this->viteManifest) {
            $this->viteManifest = json_decode(
                file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/build/manifest.json'),
                true
            );
        }

        if (isset($this->viteManifest[$assetPath]) && isset($this->viteManifest[$assetPath]['file'])) {
            return '/build/' . $this->viteManifest[$assetPath]['file'];
        }

        return $assetPath;
    }
}
