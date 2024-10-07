<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Validator;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Client\ClientInterface;
use Webgriffe\SyliusClerkPlugin\Provider\ApiKeysProviderInterface;

final readonly class RequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private ApiKeysProviderInterface $apiKeysProvider,
        private ClientInterface $client,
    ) {
    }

    public function isValid(
        ChannelInterface $channel,
        string $localeCode,
        string $authToken,
    ): bool {
        $publicKey = $this->apiKeysProvider->getPublicApiKey($channel, $localeCode);
        $verify = $this->client->verify($publicKey, $authToken);

        return $verify->isValid();
    }
}
