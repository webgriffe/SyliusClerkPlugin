<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyCheckerInterface;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProviderInterface;
use Webmozart\Assert\Assert;

final class TrackingCodeController extends AbstractController
{
    private ?ChannelApiKeyCheckerInterface $channelApiKeyChecker;

    public function __construct(
        private ChannelContextInterface $channelContext,
        private PublicApiKeyProviderInterface $publicApiKeyProvider,
        ChannelApiKeyCheckerInterface $channelApiKeyChecker = null
    ) {
        if ($channelApiKeyChecker === null) {
            trigger_deprecation(
                'webgriffe/sylius-clerk-plugin',
                '2.2',
                'Not passing a channel api key checker to "%s" is deprecated and will be removed in %s.',
                __CLASS__,
                '3.0'
            );
        }
        $this->channelApiKeyChecker = $channelApiKeyChecker;
    }

    public function trackingCodeAction(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        if ($this->channelApiKeyChecker !== null && !$this->channelApiKeyChecker->check($channel)) {
            return new Response();
        }

        return $this->render(
            '@WebgriffeSyliusClerkPlugin/trackingCode.html.twig',
            ['publicApiKey' => $this->publicApiKeyProvider->providePublicApiKeyForChannel($channel)]
        );
    }
}
