<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

final class ChannelApiKeyChecker implements ChannelApiKeyCheckerInterface
{
    private PublicApiKeyProviderInterface $publicApiKeyProvider;

    private PrivateApiKeyProviderInterface $privateApiKeyProvider;

    public function __construct(
        PublicApiKeyProviderInterface $publicApiKeyProvider,
        PrivateApiKeyProviderInterface $privateApiKeyProvider
    ) {
        $this->publicApiKeyProvider = $publicApiKeyProvider;
        $this->privateApiKeyProvider = $privateApiKeyProvider;
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
