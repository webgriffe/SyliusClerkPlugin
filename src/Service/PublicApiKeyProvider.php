<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;

final class PublicApiKeyProvider implements PublicApiKeyProviderInterface
{
    /** @var array */
    private $clerkStores;

    public function __construct(array $clerkStores)
    {
        $this->clerkStores = $clerkStores;
    }

    /**
     * @inheritdoc
     */
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
