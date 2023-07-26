<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface;
use Webmozart\Assert\Assert;

final class OrderResolver implements OrderResolverInterface
{
    public function __construct(
        private QueryBuilderFactoryInterface $ordersQueryBuilderFactory,
    ) {
    }

    public function createOrdersList(ChannelInterface $channel): array
    {
        $ordersQueryBuilder = $this->ordersQueryBuilderFactory->createQueryBuilder($channel);

        $result = $ordersQueryBuilder->getQuery()->getResult();
        Assert::isArray($result);

        return $result;
    }
}
