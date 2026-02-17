<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;

interface ApiKeysProviderInterface
{
    /**
     * @throws ChannelApiKeysNotProvidedException If the configuration for the given channel is not found.
     */
    public function getPublicApiKey(
        ChannelInterface $channel,
        string $localeCode,
    ): string;

    /**
     * @throws ChannelApiKeysNotProvidedException If the configuration for the given channel is not found.
     */
    public function getPrivateApiKey(
        ChannelInterface $channel,
        string $localeCode,
    ): string;
}
