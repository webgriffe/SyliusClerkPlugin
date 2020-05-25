<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;

final class PrivateApiKeyProvider implements PrivateApiKeyProviderInterface
{
    /** @var array */
    private $clerkStores;

    public function __construct(array $clerkStores)
    {
        $this->clerkStores = $clerkStores;
    }

    /**
     * {@inheritdoc}
     */
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
