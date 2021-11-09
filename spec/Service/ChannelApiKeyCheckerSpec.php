<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Exception\PrivateApiKeyNotFoundForChannelException;
use Webgriffe\SyliusClerkPlugin\Exception\PublicApiKeyNotFoundForChannelException;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyChecker;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyCheckerInterface;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProviderInterface;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProviderInterface;

class ChannelApiKeyCheckerSpec extends ObjectBehavior
{
    public function let(
        ChannelInterface $channel,
        PublicApiKeyProviderInterface $publicApiKeyProvider,
        PrivateApiKeyProviderInterface $privateApiKeyProvider
    ): void {
        $channel->getCode()->willReturn('Default');

        $publicApiKeyProvider->providePublicApiKeyForChannel($channel)->willReturn('ASDASD');
        $privateApiKeyProvider->providePrivateApiKeyForChannel($channel)->willReturn('ASDASD');

        $this->beConstructedWith(
            $publicApiKeyProvider,
            $privateApiKeyProvider
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ChannelApiKeyChecker::class);
    }

    public function it_implements_channel_api_key_checker_interface(): void
    {
        $this->shouldHaveType(ChannelApiKeyCheckerInterface::class);
    }

    public function it_returns_true_if_channel_code_is_found_on_provided_clerk_stores(
        ChannelInterface $channel
    ): void {
        $this->check($channel)->shouldReturn(true);
    }

    public function it_returns_true_if_channel_public_key_is_not_provided_but_private_key_exists(
        ChannelInterface $channel,
        PublicApiKeyProviderInterface $publicApiKeyProvider
    ): void {
        $publicApiKeyProvider->providePublicApiKeyForChannel($channel)->willThrow(new PublicApiKeyNotFoundForChannelException($channel->getWrappedObject()));
        $this->check($channel)->shouldReturn(true);
    }

    public function it_returns_true_if_channel_private_key_is_not_provided_but_public_key_exists(
        ChannelInterface $channel,
        PrivateApiKeyProviderInterface $privateApiKeyProvider
    ): void {
        $privateApiKeyProvider->providePrivateApiKeyForChannel($channel)->willThrow(new PrivateApiKeyNotFoundForChannelException($channel->getWrappedObject()));
        $this->check($channel)->shouldReturn(true);
    }

    public function it_returns_false_if_channel_private_and_public_keys_are_provided(
        ChannelInterface $channel,
        PublicApiKeyProviderInterface $publicApiKeyProvider,
        PrivateApiKeyProviderInterface $privateApiKeyProvider
    ): void {
        $publicApiKeyProvider->providePublicApiKeyForChannel($channel)->willThrow(new PublicApiKeyNotFoundForChannelException($channel->getWrappedObject()));
        $privateApiKeyProvider->providePrivateApiKeyForChannel($channel)->willThrow(new PrivateApiKeyNotFoundForChannelException($channel->getWrappedObject()));
        $this->check($channel)->shouldReturn(false);
    }
}
