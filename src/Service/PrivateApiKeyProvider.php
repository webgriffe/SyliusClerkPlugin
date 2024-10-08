<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
final class PrivateApiKeyProvider implements PrivateApiKeyProviderInterface
{
    public function __construct(private array $clerkStores)
    {
    }

    public function providePrivateApiKeyForChannel(ChannelInterface $channel): string
    {
        foreach ($this->clerkStores as $clerkStore) {
            if ($clerkStore['channel_code'] === $channel->getCode()) {
                return $clerkStore['private_api_key'];
            }
        }

        throw new PrivateApiKeyNotFoundForChannelException($channel);
    }
}
