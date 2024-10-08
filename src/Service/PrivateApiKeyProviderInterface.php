<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
interface PrivateApiKeyProviderInterface
{
    /**
     * @throws PrivateApiKeyNotFoundForChannelException
     */
    public function providePrivateApiKeyForChannel(ChannelInterface $channel): string;
}
