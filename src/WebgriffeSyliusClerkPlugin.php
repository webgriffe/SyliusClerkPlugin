<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class WebgriffeSyliusClerkPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        return __DIR__;
    }
}
