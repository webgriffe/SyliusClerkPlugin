<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

interface PublicApiKeyProviderInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return string
     *
     * @throws PublicApiKeyNotFoundForChannelException
     */
    public function providePublicApiKeyForChannel(ChannelInterface $channel): string;
}
