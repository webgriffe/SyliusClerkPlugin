<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface;

final class OrderResolver implements OrderResolverInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $ordersQueryBuilderFactory,
    ) {
    }

    public function createOrdersList(ChannelInterface $channel): array
    {
        $ordersQueryBuilder = $this->ordersQueryBuilderFactory->createQueryBuilder($channel);

        return $ordersQueryBuilder->getQuery()->getResult();
    }
}
