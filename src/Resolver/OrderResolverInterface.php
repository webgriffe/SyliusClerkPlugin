<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;

interface OrderResolverInterface
{
    public function createOrdersList(ChannelInterface $channel): array;
}
