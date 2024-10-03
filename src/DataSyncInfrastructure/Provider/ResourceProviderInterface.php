<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider;

use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @template T
 */
interface ResourceProviderInterface
{
    /**
     * @return T[]
     */
    public function provide(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array;
}
