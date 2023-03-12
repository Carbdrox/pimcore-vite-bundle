<?php declare(strict_types=1);

namespace ViteBundle\Twig;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use ViteBundle\Services\ViteService;

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
        return 'vite_extension';
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('viteReload', [$this, 'viteReload'], ['needs_environment' => false]),
            new TwigFunction('vite', [$this, 'getAsset'], ['needs_environment' => false])
        ];
    }

    /**
     * @param bool $editmode
     * @return string
     * @throws \Exception
     */
    public function viteReload(bool $editmode = false): string
    {
        if (!$this->viteService->hasHotReload() || $editmode) {
            return '';
        }

        return sprintf(
            '<script type="module" defer src="%s"></script>',
            $this->viteService->getAsset('@vite/client')
        );
    }

    /**
     * @param string $assetPath
     * @return string
     * @throws \Exception
     */
    public function getAsset(string $assetPath): string
    {
        return $this->viteService->getAsset($assetPath);
    }
}
