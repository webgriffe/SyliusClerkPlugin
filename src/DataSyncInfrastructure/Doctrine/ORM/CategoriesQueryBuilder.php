<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event\QueryBuilderEvent;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @implements QueryBuilderInterface<TaxonInterface>
 */
final readonly class CategoriesQueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private TaxonRepository $taxonRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function getResource(): Resource
    {
        return Resource::CATEGORIES;
    }

    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->taxonRepository->createQueryBuilder('t');

        $queryBuilder
            ->leftJoin('t.translations', 'tt', 'WITH', 'tt.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;

        if ($modifiedAfter !== null) {
            $queryBuilder
                ->andWhere('t.updatedAt > :modifiedAfter')
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

        /** @var TaxonInterface[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
