<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webmozart\Assert\Assert;

if (Kernel::MAJOR_VERSION < 5) {
    final class ProductNormalizer implements NormalizerInterface
    {
        /** @var ProductVariantResolverInterface */
        private $productVariantResolver;

        /** @var RouterInterface */
        private $router;

        /** @var FilterService */
        private $imagineFilterService;

        public function __construct(
            ProductVariantResolverInterface $productVariantResolver,
            RouterInterface $router,
            FilterService $imagineFilterService
        ) {
            $this->productVariantResolver = $productVariantResolver;
            $this->router = $router;
            $this->imagineFilterService = $imagineFilterService;
        }

        /**
         * @inheritdoc
         */
        public function normalize($object, $format = null, array $context = [])
        {
            if (!array_key_exists('channel', $context)) {
                throw new InvalidArgumentException('This normalizer needs a channel in the context');
            }
            Assert::isInstanceOf($object, ProductInterface::class);
            Assert::isInstanceOf($context['channel'], ChannelInterface::class);
            /** @var ProductInterface $product */
            $product = $object;
            /** @var ChannelInterface $channel */
            $channel = $context['channel'];
            $price = null;
            $originalPrice = null;
            $productDefaultVariant = $this->productVariantResolver->getVariant($product);
            if ($productDefaultVariant instanceof ProductVariantInterface) {
                $channelPricing = $productDefaultVariant->getChannelPricingForChannel($channel);
                if ($channelPricing instanceof ChannelPricingInterface && $channelPricing->getPrice() !== null) {
                    $price = $channelPricing->getPrice() / 100;
                    if ($channelPricing->getOriginalPrice() !== null) {
                        $originalPrice = $channelPricing->getOriginalPrice() / 100;
                    }
                }
            }

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getCode(),
                'price' => $price,
                'url' => $this->router->generate(
                    'sylius_shop_product_show',
                    ['slug' => $product->getSlug(), '_locale' => $product->getTranslation()->getLocale()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'categories' => $this->getTaxonsIds($product),
            ];
            if ($product->getDescription() !== null) {
                $productData['description'] = $product->getDescription();
            }
            $mainImage = $product->getImagesByType('main')->first();
            if ($mainImage instanceof ImageInterface && $mainImage->getPath() !== null) {
                $productData['image'] = $this->imagineFilterService->getUrlOfFilteredImage(
                    $mainImage->getPath(),
                    'sylius_shop_product_thumbnail'
                );
            }
            if ($originalPrice !== null) {
                $productData['list_price'] = $originalPrice;
            }
            foreach ($product->getAttributes() as $attribute) {
                $productData[$attribute->getCode()] = $attribute->getValue();
            }

            return $productData;
        }

        /**
         * @inheritdoc
         */
        public function supportsNormalization($data, $format = null)
        {
            return $data instanceof ProductInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
        }

        private function getTaxonsIds(ProductInterface $product): array
        {
            $taxonsIds = [];
            foreach ($product->getTaxons() as $taxon) {
                $taxonsIds[] = $taxon->getId();
            }

            return $taxonsIds;
        }
    }
} else {
    final class ProductNormalizer implements NormalizerInterface
    {
        /** @var ProductVariantResolverInterface */
        private $productVariantResolver;

        /** @var RouterInterface */
        private $router;

        /** @var FilterService */
        private $imagineFilterService;

        public function __construct(
            ProductVariantResolverInterface $productVariantResolver,
            RouterInterface $router,
            FilterService $imagineFilterService
        ) {
            $this->productVariantResolver = $productVariantResolver;
            $this->router = $router;
            $this->imagineFilterService = $imagineFilterService;
        }

        /**
         * @inheritdoc
         */
        public function normalize($object, string $format = null, array $context = [])
        {
            if (!array_key_exists('channel', $context)) {
                throw new InvalidArgumentException('This normalizer needs a channel in the context');
            }
            Assert::isInstanceOf($object, ProductInterface::class);
            Assert::isInstanceOf($context['channel'], ChannelInterface::class);
            /** @var ProductInterface $product */
            $product = $object;
            /** @var ChannelInterface $channel */
            $channel = $context['channel'];
            $price = null;
            $originalPrice = null;
            $productDefaultVariant = $this->productVariantResolver->getVariant($product);
            if ($productDefaultVariant instanceof ProductVariantInterface) {
                $channelPricing = $productDefaultVariant->getChannelPricingForChannel($channel);
                if ($channelPricing instanceof ChannelPricingInterface && $channelPricing->getPrice() !== null) {
                    $price = $channelPricing->getPrice() / 100;
                    if ($channelPricing->getOriginalPrice() !== null) {
                        $originalPrice = $channelPricing->getOriginalPrice() / 100;
                    }
                }
            }

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getCode(),
                'price' => $price,
                'url' => $this->router->generate(
                    'sylius_shop_product_show',
                    ['slug' => $product->getSlug(), '_locale' => $product->getTranslation()->getLocale()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'categories' => $this->getTaxonsIds($product),
            ];
            if ($product->getDescription() !== null) {
                $productData['description'] = $product->getDescription();
            }
            $mainImage = $product->getImagesByType('main')->first();
            if ($mainImage instanceof ImageInterface && $mainImage->getPath() !== null) {
                $productData['image'] = $this->imagineFilterService->getUrlOfFilteredImage(
                    $mainImage->getPath(),
                    'sylius_shop_product_thumbnail'
                );
            }
            if ($originalPrice !== null) {
                $productData['list_price'] = $originalPrice;
            }
            foreach ($product->getAttributes() as $attribute) {
                $productData[$attribute->getCode()] = $attribute->getValue();
            }

            return $productData;
        }

        /**
         * @inheritdoc
         */
        public function supportsNormalization($data, string $format = null)
        {
            return $data instanceof ProductInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
        }

        private function getTaxonsIds(ProductInterface $product): array
        {
            $taxonsIds = [];
            foreach ($product->getTaxons() as $taxon) {
                $taxonsIds[] = $taxon->getId();
            }

            return $taxonsIds;
        }
    }
}
