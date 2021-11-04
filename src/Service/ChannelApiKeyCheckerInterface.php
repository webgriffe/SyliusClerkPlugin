<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;

interface ChannelApiKeyCheckerInterface
{
    public function check(ChannelInterface $channel): bool;
}
