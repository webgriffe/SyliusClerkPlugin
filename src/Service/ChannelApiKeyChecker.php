<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
final class ChannelApiKeyChecker implements ChannelApiKeyCheckerInterface
{
    public function __construct(
        private PublicApiKeyProviderInterface $publicApiKeyProvider,
        private PrivateApiKeyProviderInterface $privateApiKeyProvider,
    ) {
    }

    public function check(ChannelInterface $channel): bool
    {
        try {
            $this->privateApiKeyProvider->providePrivateApiKeyForChannel($channel);
        } catch (PrivateApiKeyNotFoundForChannelException $e) {
            try {
                $this->publicApiKeyProvider->providePublicApiKeyForChannel($channel);
            } catch (PublicApiKeyNotFoundForChannelException $e) {
                return false;
            }
        }

        return true;
    }
}
