<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

interface PageResolverInterface
{
    public function createPageList(): array;
}
