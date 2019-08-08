<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;

interface FeedGeneratorInterface
{
    public function generate(ChannelInterface $channel): string;
}
