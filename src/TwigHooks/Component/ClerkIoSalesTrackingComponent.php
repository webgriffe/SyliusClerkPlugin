<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\TwigHooks\Component;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @psalm-suppress UnusedClass
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[AsTwigComponent]
final class ClerkIoSalesTrackingComponent
{
    use HookableComponentTrait;

    public OrderInterface $order;

    public function __construct(
        private readonly bool $useProductVariants = false,
    ) {
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    #[ExposeInTemplate('order_id')]
    public function getOrderId(): string|int
    {
        return $this->order->getId();
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    #[ExposeInTemplate('customer_id')]
    public function getCustomerId(): string|int|null
    {
        return $this->order->getCustomer()?->getId();
    }

    /**
     * @psalm-suppress MixedReturnStatement
     */
    #[ExposeInTemplate('customer_email')]
    public function getCustomerEmail(): ?string
    {
        return $this->order->getCustomer()?->getEmail();
    }

    /**
     * @return array<array-key, array{id: string|int, quantity: int, price: float}>
     */
    #[ExposeInTemplate('products')]
    public function getProducts(): array
    {
        $products = [];
        foreach ($this->order->getItems() as $item) {
            $productVariant = $item->getVariant();
            if (!$productVariant instanceof ProductVariantInterface) {
                continue;
            }
            $product = $item->getProduct();
            if (!$product instanceof ProductInterface) {
                continue;
            }
            /** @var int|string $productId */
            $productId = $this->useProductVariants ? $productVariant->getId() : $product->getId();

            $products[] = [
                'id' => $productId,
                'quantity' => $item->getQuantity(),
                'price' => $item->getUnitPrice() / 100,
            ];
        }

        return $products;
    }
}
