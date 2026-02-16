<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event\QueryBuilderEvent;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @implements QueryBuilderInterface<ProductVariantInterface>
 */
final readonly class ProductVariantsQueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private ProductVariantRepository $productVariantRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[\Override]
    public function getResource(): Resource
    {
        return Resource::PRODUCTS;
    }

    #[\Override]
    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->productVariantRepository->createQueryBuilder('pv');

        $queryBuilder
            ->join('pv.product', 'p')
            ->andWhere('pv.enabled = true')
            ->leftJoin('pv.translations', 'pvt', 'WITH', 'pvt.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;
        $queryBuilder
            ->andWhere(':channel MEMBER OF p.channels')
            ->andWhere('p.enabled = true')
            ->setParameter('channel', $channel)
            ->leftJoin('p.translations', 'pt', 'WITH', 'pt.locale = :localeCode')
        ;

        if ($modifiedAfter !== null) {
            $queryBuilder
                ->andWhere('pv.updatedAt > :modifiedAfter')
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

        /** @var ProductVariantInterface[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
