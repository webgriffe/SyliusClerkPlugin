<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use the new Clerk feed v2 normalizer instead.
 */
final class OrderNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof OrderInterface) {
            throw new InvalidArgumentException('This normalizer supports only instances of ' . OrderInterface::class);
        }
        $order = $object;
        $products = [];
        foreach ($order->getItems() as $item) {
            $productVariant = $item->getVariant();
            if ($productVariant === null) {
                continue;
            }
            $product = $productVariant->getProduct();
            if ($product === null) {
                continue;
            }
            $products[] = [
                'id' => $product->getId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getUnitPrice() / 100,
            ];
        }

        $checkoutCompletedAt = $order->getCheckoutCompletedAt();
        $customer = $order->getCustomer();

        return [
            'id' => $order->getId(),
            'products' => $products,
            'time' => $checkoutCompletedAt instanceof \DateTimeInterface ? $checkoutCompletedAt->getTimestamp() : null,
            'email' => $customer instanceof CustomerInterface ? $customer->getEmail() : null,
            'customer' => $customer instanceof CustomerInterface ? $customer->getId() : null,
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof OrderInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
    }
}
