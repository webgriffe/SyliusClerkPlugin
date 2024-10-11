<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderNormalizerEvent
{
    /**
     * @param array{
     *      id: string|int,
     *      customer?: string|int,
     *      email?: string,
     *      products: array<array-key, array{id: string|int, quantity: int, price: float}>,
     *      time: int,
     * }&array<string, mixed> $orderData
     */
    public function __construct(
        private array $orderData,
        private readonly OrderInterface $order,
        private readonly ChannelInterface $channel,
        private readonly string $localeCode,
        private readonly array $context,
    ) {
    }

    /**
     * @return array{
     *      id: string|int,
     *      customer?: string|int,
     *      email?: string,
     *      products: array<array-key, array{id: string|int, quantity: int, price: float}>,
     *      time: int,
     *  }&array<string, mixed>
     */
    public function getOrderData(): array
    {
        return $this->orderData;
    }

    /**
     * @param array{
     *      id: string|int,
     *      customer?: string|int,
     *      email?: string,
     *      products: array<array-key, array{id: string|int, quantity: int, price: float}>,
     *      time: int,
     *  }&array<string, mixed> $orderData
     */
    public function setOrderData(array $orderData): void
    {
        $this->orderData = $orderData;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
