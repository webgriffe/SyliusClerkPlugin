<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
interface FeedGeneratorInterface
{
    public function generate(ChannelInterface $channel): string;
}
