<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
interface PublicApiKeyProviderInterface
{
    /**
     * @throws PublicApiKeyNotFoundForChannelException
     */
    public function providePublicApiKeyForChannel(ChannelInterface $channel): string;
}
