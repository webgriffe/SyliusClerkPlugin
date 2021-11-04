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
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var PublicApiKeyProviderInterface */
    private $publicApiKeyProvider;

    private ChannelApiKeyCheckerInterface $channelApiKeyChecker;

    public function __construct(
        ChannelContextInterface $channelContext,
        PublicApiKeyProviderInterface $publicApiKeyProvider,
        ChannelApiKeyCheckerInterface $channelApiKeyChecker
    ) {
        $this->publicApiKeyProvider = $publicApiKeyProvider;
        $this->channelContext = $channelContext;
        $this->channelApiKeyChecker = $channelApiKeyChecker;
    }

    public function trackingCodeAction(Request $request): Response
    {
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (!$this->channelApiKeyChecker->check($channel)) {
            return new Response();
        }

        return $this->render(
            '@WebgriffeSyliusClerkPlugin/trackingCode.html.twig',
            ['publicApiKey' => $this->publicApiKeyProvider->providePublicApiKeyForChannel($channel)]
        );
    }
}
