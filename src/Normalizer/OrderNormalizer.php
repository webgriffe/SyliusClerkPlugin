<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

// @phpstan-ignore-next-line
if (Kernel::MAJOR_VERSION === 4) {
    final class OrderNormalizer implements NormalizerInterface
    {
        /**
         * @inheritdoc
         * @phpstan-ignore-next-line
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

        /**
         * @inheritdoc
         * @phpstan-ignore-next-line
         */
        public function supportsNormalization($data, $format = null)
        {
            return $data instanceof OrderInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
        }
    }
} else {
    final class OrderNormalizer implements NormalizerInterface
    {
        /**
         * @inheritdoc
         * @phpstan-ignore-next-line
         */
        public function normalize($object, string $format = null, array $context = [])
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

        /**
         * @inheritdoc
         * @phpstan-ignore-next-line
         */
        public function supportsNormalization($data, string $format = null) // @phpstan-ignore-line
        {
            return $data instanceof OrderInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
        }
    }
}
