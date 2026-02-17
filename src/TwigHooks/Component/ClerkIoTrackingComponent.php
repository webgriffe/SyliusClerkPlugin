<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\TwigHooks\Component;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Webgriffe\SyliusClerkPlugin\Provider\ApiKeysProviderInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;
use Webmozart\Assert\Assert;

/**
 * @psalm-suppress UnusedClass
 */
#[AsTwigComponent]
final class ClerkIoTrackingComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate(name: 'public_api_key')]
    public ?string $publicApiKey = null;

    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly ApiKeysProviderInterface $apiKeysProvider,
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $localeCode = $this->localeContext->getLocaleCode();

        try {
            $apiKey = $this->apiKeysProvider->getPublicApiKey($channel, $localeCode);
        } catch (ChannelApiKeysNotProvidedException) {
            return;
        }

        $this->publicApiKey = $apiKey;
    }
}
