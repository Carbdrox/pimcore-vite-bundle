<?php declare(strict_types=1);

namespace ViteBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class ViteBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * @return string
     */
    protected function getComposerPackageName(): string
    {
        return 'carbdrox/pimcore-vite-bundle';
    }
}
