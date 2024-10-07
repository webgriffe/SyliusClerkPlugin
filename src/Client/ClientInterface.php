<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Client;

use Webgriffe\SyliusClerkPlugin\Client\Exception\ClientException;
use Webgriffe\SyliusClerkPlugin\Client\Response\Verify;

interface ClientInterface
{
    /**
     * @throws ClientException
     */
    public function verify(
        string $storePublicKey,
        string $token,
    ): Verify;
}
