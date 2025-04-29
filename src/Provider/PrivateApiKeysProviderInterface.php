<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;

interface PrivateApiKeysProviderInterface
{
    /**
     * @throws ChannelApiKeysNotProvidedException If the configuration for the given channel is not found.
     */
    public function getPrivateApiKey(
        ChannelInterface $channel,
        string $localeCode,
    ): string;
}
