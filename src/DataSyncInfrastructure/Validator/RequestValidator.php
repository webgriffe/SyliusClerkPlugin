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

    #[\Override]
    public function isValid(
        ChannelInterface $channel,
        string $localeCode,
        string $authToken,
    ): bool {
        $publicKey = $this->apiKeysProvider->getPublicApiKey($channel, $localeCode);

        return $this->client->verify($publicKey, $authToken)->isValid();
    }
}
