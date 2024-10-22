<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event\QueryBuilderEvent;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @implements QueryBuilderInterface<ProductInterface>
 */
final readonly class ProductsQueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getResource(): Resource
    {
        return Resource::PRODUCTS;
    }

    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->productRepository->createQueryBuilder('p');

        $queryBuilder
            ->andWhere(':channel MEMBER OF p.channels')
            ->andWhere('p.enabled = true')
            ->setParameter('channel', $channel)
        ;
        $queryBuilder
            ->leftJoin('p.translations', 't', 'WITH', 't.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;

        if ($modifiedAfter !== null) {
            $queryBuilder
                ->andWhere('p.updatedAt > :modifiedAfter')
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
            $this->getResource(),
            $queryBuilder,
            $channel,
            $localeCode,
            $modifiedAfter,
            $limit,
            $offset,
        ));

        /** @var ProductInterface[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
