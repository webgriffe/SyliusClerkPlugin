<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Validator;

use Sylius\Component\Core\Model\ChannelInterface;

interface RequestValidatorInterface
{
    public function isValid(
        ChannelInterface $channel,
        string $localeCode,
        string $authToken,
    ): bool;
}
