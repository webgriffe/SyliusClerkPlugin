<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event\QueryBuilderEvent;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @implements QueryBuilderInterface<CustomerInterface>
 */
final readonly class CustomersQueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[\Override]
    public function getResource(): Resource
    {
        return Resource::CUSTOMERS;
    }

    #[\Override]
    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->customerRepository->createQueryBuilder('c');
        $queryBuilder
            ->andWhere('(c.firstName IS NOT NULL OR c.lastName IS NOT NULL)')
        ;

        if ($modifiedAfter !== null) {
            $queryBuilder
                ->andWhere('c.updatedAt > :modifiedAfter')
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

        /** @var CustomerInterface[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
