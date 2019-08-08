<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProviderInterface;
use Webmozart\Assert\Assert;

final class TrackingCodeController extends AbstractController
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;
    /**
     * @var PublicApiKeyProviderInterface
     */
    private $publicApiKeyProvider;

    public function __construct(
        ChannelContextInterface $channelContext,
        PublicApiKeyProviderInterface $publicApiKeyProvider
    ) {
        $this->publicApiKeyProvider = $publicApiKeyProvider;
        $this->channelContext = $channelContext;
    }

    public function trackingCodeAction(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $this->render(
            '@WebgriffeSyliusClerkPlugin/trackingCode.html.twig',
            ['publicApiKey' => $this->publicApiKeyProvider->providePublicApiKeyForChannel($channel)]
        );
    }
}
