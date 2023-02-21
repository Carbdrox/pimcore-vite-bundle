<?php declare(strict_types=1);

namespace Carbdrox\Pimcore\ViteBundle\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Carbdrox\Pimcore\ViteBundle\Services\ViteService;

class ViteExtension extends AbstractExtension
{

    private ViteService $viteService;

    public function __construct(ViteService $viteService)
    {
        $this->viteService = $viteService;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'carbdrox_vite_extension';
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasHotReload', [$this, 'hasHotReload'], ['needs_environment' => false]),
            new TwigFunction('vite', [$this, 'getViteAsset'], ['needs_environment' => false])
        ];
    }

    /**
     * @return bool
     */
    public function hasHotReload(): bool
    {
        return $this->viteService->hasHotReload();
    }

    /**
     * @param string $assetPath
     * @return string
     */
    public function getViteAsset(string $assetPath): string
    {
        return $this->viteService->getViteAsset($assetPath);
    }
}
