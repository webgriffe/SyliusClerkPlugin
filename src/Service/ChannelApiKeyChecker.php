<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelApiKeyChecker implements ChannelApiKeyCheckerInterface
{
    private array $clerkStores;

    public function __construct(array $clerkStores)
    {
        $this->clerkStores = $clerkStores;
    }

    public function check(ChannelInterface $channel): bool
    {
        /** @var array $clerkStore */
        foreach ($this->clerkStores as $clerkStore) {
            if ($clerkStore['channel_code'] === $channel->getCode()) {
                return true;
            }
        }

        return false;
    }
}
