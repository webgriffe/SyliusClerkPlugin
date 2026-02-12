<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;

final readonly class ApiKeysProvider implements ApiKeysProviderInterface
{
    /**
     * @param array<array-key, array{
     *     channel_code: string,
     *     public_api_key: string,
     *     private_api_key: string,
     *     locale_code?: string,
     * }> $storesConfiguration
     */
    public function __construct(
        private array $storesConfiguration,
    ) {
    }

    public function getPublicApiKey(
        ChannelInterface $channel,
        string $localeCode,
    ): string {
        $channelsConfiguration = array_filter(
            $this->storesConfiguration,
            fn (array $storeConfiguration) => $storeConfiguration['channel_code'] === (string) $channel->getCode(),
        );
        $channelCode = (string) $channel->getCode();
        if (count($channelsConfiguration) === 0) {
            throw new ChannelApiKeysNotProvidedException('Configuration not found for channel ' . $channelCode);
        }
        $atLeastOneConfigurationHasLocaleCode = array_reduce(
            $channelsConfiguration,
            fn (bool $carry, array $storeConfiguration) => $carry || array_key_exists('locale_code', $storeConfiguration),
            false,
        );
        if ($atLeastOneConfigurationHasLocaleCode) {
            $channelLocalesConfiguration = array_filter(
                $channelsConfiguration,
                fn (array $storeConfiguration) => array_key_exists('locale_code', $storeConfiguration) && $storeConfiguration['locale_code'] === $localeCode,
            );
            if (count($channelLocalesConfiguration) === 0) {
                throw new ChannelApiKeysNotProvidedException('Configuration not found for channel ' . $channelCode . ' and locale ' . $localeCode);
            }
            if (count($channelLocalesConfiguration) > 1) {
                throw new ChannelApiKeysNotProvidedException('Multiple configurations found for channel ' . $channelCode . ' and locale ' . $localeCode);
            }

            return reset($channelLocalesConfiguration)['public_api_key'];
        }
        if (count($channelsConfiguration) > 1) {
            throw new ChannelApiKeysNotProvidedException('Multiple configurations found for channel ' . $channelCode);
        }

        return reset($channelsConfiguration)['public_api_key'];
    }
}
