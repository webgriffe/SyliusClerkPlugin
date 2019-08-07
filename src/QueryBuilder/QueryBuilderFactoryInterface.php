<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;

interface QueryBuilderFactoryInterface
{
    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder;
}
