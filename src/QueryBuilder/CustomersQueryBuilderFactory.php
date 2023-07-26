<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomersQueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        Assert::isInstanceOf($this->customerRepository, EntityRepository::class);

        return $this->customerRepository
            ->createQueryBuilder('c')
            ->where('c.email IS NOT NULL');
    }
}
