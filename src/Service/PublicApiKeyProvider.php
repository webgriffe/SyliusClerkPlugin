<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
final class PublicApiKeyProvider implements PublicApiKeyProviderInterface
{
    public function __construct(private array $clerkStores)
    {
    }

    public function providePublicApiKeyForChannel(ChannelInterface $channel): string
    {
        foreach ($this->clerkStores as $clerkStore) {
            if ($clerkStore['channel_code'] === $channel->getCode()) {
                return $clerkStore['public_api_key'];
            }
        }

        throw new PublicApiKeyNotFoundForChannelException($channel);
    }
}
