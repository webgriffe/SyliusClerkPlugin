<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
interface ChannelApiKeyCheckerInterface
{
    public function check(ChannelInterface $channel): bool;
}
