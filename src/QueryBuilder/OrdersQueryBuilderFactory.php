<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use query builder event on v2 feed generation instead.
 */
final class OrdersQueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    #[\Override]
    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        Assert::isInstanceOf($this->orderRepository, EntityRepository::class);
        $ordersQueryBuilder = $this->orderRepository
            ->createQueryBuilder('o')
            ->where('o.channel', ':channel')
            ->where('o.checkoutCompletedAt IS NOT NULL');

        return $ordersQueryBuilder;
    }
}
