<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use query builder event on v2 feed generation instead.
 */
final class TaxonsQueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(private TaxonRepositoryInterface $taxonRepository)
    {
    }

    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        return $this->taxonRepository->createListQueryBuilder();
    }
}
