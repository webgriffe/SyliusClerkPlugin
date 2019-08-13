<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class SalesTrackingController extends AbstractController
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(OrderRepositoryInterface $orderRepository, NormalizerInterface $normalizer)
    {
        $this->orderRepository = $orderRepository;
        $this->normalizer = $normalizer;
    }

    public function indexAction(int $orderId, Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($orderId);
        Assert::isInstanceOf($order, OrderInterface::class);
        $order = $this->normalizer->normalize($order, 'json', ['channel' => $order->getChannel()]);

        return $this->render('@WebgriffeSyliusClerkPlugin/salesTracking.html.twig', ['order' => $order]);
    }
}
