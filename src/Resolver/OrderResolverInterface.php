<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use resource provider on v2 feed generation instead.
 */
interface OrderResolverInterface
{
    public function createOrdersList(ChannelInterface $channel): array;
}
