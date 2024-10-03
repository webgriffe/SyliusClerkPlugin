<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;

/**
 * @template T
 * @implements ResourceProviderInterface<T>
 */
final readonly class QueryBuilderResourceProvider implements ResourceProviderInterface
{
    public function __construct(
        private QueryBuilderInterface $queryBuilder,
    ) {
    }

    public function provide(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        return $this->queryBuilder->getResult(
            $channel,
            $localeCode,
            $modifiedAfter,
            $limit,
            $offset,
        );
    }
}
