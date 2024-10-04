<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event\QueryBuilderEvent;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @implements QueryBuilderInterface<OrderInterface>
 */
final readonly class OrdersQueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getResource(): Resource
    {
        return Resource::ORDERS;
    }

    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');

        $queryBuilder
            ->andWhere('o.channel = :channel')
            ->setParameter('channel', $channel)
            ->andWhere('o.checkoutCompletedAt IS NOT NULL')
        ;

        if ($modifiedAfter !== null) {
            $queryBuilder
                ->andWhere('o.updatedAt > :modifiedAfter')
                ->setParameter('modifiedAfter', $modifiedAfter)
            ;
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        $this->eventDispatcher->dispatch(new QueryBuilderEvent(
            $queryBuilder,
            $channel,
            $localeCode,
            $modifiedAfter,
            $limit,
            $offset,
        ));

        /** @var OrderInterface[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
