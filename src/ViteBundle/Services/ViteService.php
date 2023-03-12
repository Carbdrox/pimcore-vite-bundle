<?php declare(strict_types=1);

namespace ViteBundle\Services;

class ViteService
{
    private string $env;
    private string $projectDirectory;
    private string $hotFilePath;
    private mixed $viteManifest = null;

    public function __construct(string $env, string $projectDirectory)
    {
        $this->env = $env;
        $this->projectDirectory = $projectDirectory;
        $this->hotFilePath = $this->projectDirectory . '/public/vite-serve';
    }

    /**
     * @return bool
     */
    public function hasHotReload(): bool
    {
        return $this->env === 'dev' && file_exists($this->hotFilePath) && is_readable($this->hotFilePath);
    }

    /**
     * @param string $assetPath
     * @return string
     * @throws \Exception
     */
    public function getAsset(string $assetPath): string
    {
        if ($this->hasHotReload()) {
            return $this->getHotAssetPath($assetPath);
        }

        if ($manifestAssetPath = $this->getManifestAssetPath($assetPath)) {
            return $manifestAssetPath;
        }

        return $assetPath;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function manifest(): mixed
    {
        if (!$this->viteManifest) {
            $manifestPath = $this->projectDirectory . '/public/build/manifest.json';

            if (!file_exists($manifestPath) || !is_readable($manifestPath)) {
                throw new \Exception("Can not find vite manifest file.\nSearched at: " . $manifestPath);
            }

            $this->viteManifest = json_decode(
                file_get_contents($manifestPath),
                true
            );

            if (!$this->viteManifest || !is_array($this->viteManifest)) {
                throw new \Exception("Can not decode vite manifest file at: " . $manifestPath);
            }
        }

        return $this->viteManifest;
    }

    /**
     * @param string $assetPath
     * @return string
     */
    protected function getHotAssetPath(string $assetPath): string
    {
        return rtrim(
            sprintf(
                '%s/%s',
                file_get_contents($this->hotFilePath),
                $assetPath
            )
        );
    }

    /**
     * @param string $assetPath
     * @return string|null
     * @throws \Exception
     */
    protected function getManifestAssetPath(string $assetPath): string | null
    {
        $manifest = $this->manifest();

        if (!isset($manifest[$assetPath]) || !isset($manifest[$assetPath]['file'])) {
            return null;
        }

        return '/build/' . $manifest[$assetPath]['file'];
    }

}
