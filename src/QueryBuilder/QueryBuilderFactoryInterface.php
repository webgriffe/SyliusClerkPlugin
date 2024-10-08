<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use query builder event on v2 feed generation instead.
 */
interface QueryBuilderFactoryInterface
{
    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder;
}
