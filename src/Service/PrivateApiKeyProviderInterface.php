<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;

interface PrivateApiKeyProviderInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return string
     *
     * @throws PrivateApiKeyNotFoundForChannelException
     */
    public function providePrivateApiKeyForChannel(ChannelInterface $channel): string;
}
