<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Encoder\ClerkJsonEncoder;

final class OrderNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof OrderInterface) {
            throw new InvalidArgumentException('This normalizer supports only instances of ' . OrderInterface::class);
        }
        /** @var OrderInterface $order */
        $order = $object;
        $products = [];
        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $productId = null;
            $productVariant = $item->getVariant();
            if ($productVariant) {
                $product = $productVariant->getProduct();
                if ($product) {
                    $productId = $product->getId();
                }
            }
            $products[] = [
                'id' => $productId,
                'quantity' => $item->getQuantity(),
                'price' => $item->getUnitPrice() / 100,
            ];
        }

        $checkoutCompletedAt = $order->getCheckoutCompletedAt();
        $customer = $order->getCustomer();

        return [
            'id' => $order->getId(),
            'products' => $products,
            'time' => $checkoutCompletedAt ? $checkoutCompletedAt->getTimestamp() : null,
            'email' => $customer ? $customer->getEmail() : null,
            'customer' => $customer ? $customer->getId() : null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderInterface && $format === ClerkJsonEncoder::FORMAT;
    }
}
