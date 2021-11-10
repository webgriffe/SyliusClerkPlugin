<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyCheckerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webmozart\Assert\Assert;

final class SalesTrackingController extends AbstractController
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var NormalizerInterface */
    private $normalizer;

    private ?ChannelApiKeyCheckerInterface $channelApiKeyChecker;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        NormalizerInterface $normalizer,
        ChannelApiKeyCheckerInterface $channelApiKeyChecker = null
    ) {
        $this->orderRepository = $orderRepository;
        $this->normalizer = $normalizer;
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

    public function indexAction(int $orderId, Request $request): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);
        $channel = $order->getChannel();
        Assert::isInstanceOf($channel, ChannelInterface::class);
        if ($this->channelApiKeyChecker !== null && !$this->channelApiKeyChecker->check($channel)) {
            return new Response();
        }
        $orderNormalized = $this->normalizer->normalize(
            $order,
            FeedGenerator::NORMALIZATION_FORMAT,
            ['channel' => $channel]
        );

        return $this->render('@WebgriffeSyliusClerkPlugin/salesTracking.html.twig', ['order' => $orderNormalized]);
    }
}
