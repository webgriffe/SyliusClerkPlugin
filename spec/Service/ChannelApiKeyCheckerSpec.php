<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyChecker;
use PhpSpec\ObjectBehavior;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyCheckerInterface;

class ChannelApiKeyCheckerSpec extends ObjectBehavior
{
    public function let(
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('Default');

        $this->beConstructedWith([
            [
                'channel_code' => 'Default',
                'public_key' => 'ASDASD',
                'private_key' => 'ASDASD',
            ]
        ]);
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

    public function it_returns_false_if_channel_code_is_found_on_provided_clerk_stores(
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB_US');
        $this->check($channel)->shouldReturn(false);
    }
}
