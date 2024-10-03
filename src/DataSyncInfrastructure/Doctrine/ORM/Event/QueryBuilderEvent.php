<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\Event;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;

final readonly class QueryBuilderEvent
{
    public function __construct(
        private QueryBuilder $queryBuilder,
        private ChannelInterface $channel,
        private string $localeCode,
        private ?\DateTimeInterface $modifiedAfter = null,
        private ?int $limit = null,
        private ?int $offset = null,
    ) {
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getModifiedAfter(): ?\DateTimeInterface
    {
        return $this->modifiedAfter;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
