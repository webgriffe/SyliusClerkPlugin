<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webgriffe\SyliusClerkPlugin\Provider\ApiKeysProviderInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;
use Webmozart\Assert\Assert;

final class TrackingCodeController extends AbstractController
{
    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly ApiKeysProviderInterface $apiKeysProvider,
    ) {
    }

    public function trackingCodeAction(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $localeCode = $this->localeContext->getLocaleCode();

        try {
            $apiKey = $this->apiKeysProvider->getPublicApiKey($channel, $localeCode);
        } catch (ChannelApiKeysNotProvidedException) {
            return new Response();
        }

        return $this->render(
            '@WebgriffeSyliusClerkPlugin/trackingCode.html.twig',
            [
                'publicApiKey' => $apiKey,
        ],
        );
    }
}
