<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Provider\ApiKeysProviderInterface;
use Webgriffe\SyliusClerkPlugin\Provider\Exception\ChannelApiKeysNotProvidedException;
use Webmozart\Assert\Assert;

final class SalesTrackingController extends AbstractController
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly ApiKeysProviderInterface $apiKeysProvider,
    ) {
    }

    public function indexAction(int $orderId): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::isInstanceOf($order, OrderInterface::class);

        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $localeCode = $order->getLocaleCode();
        Assert::string($localeCode);

        try {
            $this->apiKeysProvider->getPublicApiKey($channel, $localeCode);
        } catch (ChannelApiKeysNotProvidedException) {
            return new Response();
        }

        $orderNormalized = $this->normalizer->normalize(
            $order,
            'array',
            [
                'type' => 'webgriffe_sylius_clerk_plugin',
                'channel' => $channel,
                'localeCode' => $localeCode,
            ],
        );

        return $this->render('@WebgriffeSyliusClerkPlugin/salesTracking.html.twig', [
            'order' => $orderNormalized,
        ]);
    }
}
