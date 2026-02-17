<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\OrderNormalizerEvent;
use Webmozart\Assert\Assert;

final readonly class OrderNormalizer implements NormalizerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private bool $useProductVariants = false,
    ) {
    }

    /**
     * @param OrderInterface|mixed $data
     *
     * @return array{
     *     id: string|int,
     *     customer?: string|int,
     *     email?: string,
     *     products: array<array-key, array{id: string|int, quantity: int, price: float}>,
     *     time: int,
     * }&array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $order = $data;
        Assert::isInstanceOf($order, OrderInterface::class);
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The given context should contain a ChannelInterface instance.');
        $localeCode = $context['localeCode'];
        Assert::stringNotEmpty($localeCode, 'The given context should contain a non-empty string localeCode.');

        $orderId = $order->getId();
        if (!is_string($orderId) && !is_int($orderId)) {
            throw new \InvalidArgumentException('Order ID must be a string or an integer, "' . gettype($orderId) . '" given.');
        }

        $customerId = null;
        $customer = $order->getCustomer();
        $customerEmail = null;
        if ($customer instanceof CustomerInterface) {
            /** @var int|string|mixed|null $customerId */
            $customerId = $customer->getId();
            if (!is_string($customerId) && !is_int($customerId)) {
                throw new \InvalidArgumentException('Customer ID must be a string or an integer, "' . gettype($customerId) . '" given.');
            }
            $customerEmail = $customer->getEmail();
        }
        $orderCheckoutCompletedAt = $order->getCheckoutCompletedAt();
        if ($orderCheckoutCompletedAt === null) {
            throw new \InvalidArgumentException('Order checkout completed at date cannot be null.');
        }

        $orderData = [
            'id' => $orderId,
            'products' => $this->getProducts($order),
            'time' => $orderCheckoutCompletedAt->getTimestamp(),
        ];
        if ($customerId !== null) {
            $orderData['customer'] = $customerId;
        }
        if ($customerEmail !== null) {
            $orderData['email'] = $customerEmail;
        }

        $orderNormalizerEvent = new OrderNormalizerEvent(
            $orderData,
            $order,
            $channel,
            $localeCode,
            $context,
        );
        $this->eventDispatcher->dispatch($orderNormalizerEvent);

        return $orderNormalizerEvent->getOrderData();
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof OrderInterface &&
            $format === 'array' &&
            array_key_exists('type', $context) &&
            $context['type'] === 'webgriffe_sylius_clerk_plugin'
        ;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            OrderInterface::class => true,
        ];
    }

    /**
     * @return array<array-key, array{id: string|int, quantity: int, price: float}>
     */
    private function getProducts(OrderInterface $order): array
    {
        $products = [];
        foreach ($order->getItems() as $item) {
            $products[] = $this->normalizeOrderItem($item);
        }

        return $products;
    }

    /**
     * @return array{id: string|int, quantity: int, price: float}
     */
    private function normalizeOrderItem(OrderItemInterface $item): array
    {
        if ($this->useProductVariants === true) {
            $productVariant = $item->getVariant();
            if (!$productVariant instanceof ProductVariantInterface) {
                throw new \InvalidArgumentException('Order item product variant cannot be null.');
            }
            $productId = $productVariant->getId();
            if (!is_string($productId) && !is_int($productId)) {
                throw new \InvalidArgumentException('Product variant ID must be a string or an integer, "' . gettype($productId) . '" given.');
            }
        } else {
            $product = $item->getProduct();
            if (!$product instanceof ProductInterface) {
                throw new \InvalidArgumentException('Order item product cannot be null.');
            }
            /** @var mixed $productId */
            $productId = $product->getId();
            if (!is_string($productId) && !is_int($productId)) {
                throw new \InvalidArgumentException('Product ID must be a string or an integer, "' . gettype($productId) . '" given.');
            }
        }

        return [
            'id' => $productId,
            'quantity' => $item->getQuantity(),
            'price' => $item->getUnitPrice() / 100,
        ];
    }
}
