<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Exception;

use Sylius\Component\Core\Model\ChannelInterface;

final class PrivateApiKeyNotFoundForChannelException extends \RuntimeException
{
    public function __construct(ChannelInterface $channel)
    {
        $message = sprintf(
            'Cannot find Clerk store for channel "%s". Please configure Clerk stores.',
            $channel->getCode(),
        );
        parent::__construct($message);
    }
}
