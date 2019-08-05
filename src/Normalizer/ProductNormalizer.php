<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

class ProductNormalizer implements NormalizerInterface
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $productVariantResolver;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var FilterService
     */
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
     * {@inheritdoc}
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
        if ($productDefaultVariant) {
            /** @var ProductVariantInterface $productDefaultVariant */
            Assert::isInstanceOf($productDefaultVariant, ProductVariantInterface::class);
            $channelPricing = $productDefaultVariant->getChannelPricingForChannel($channel);
            if ($channelPricing && $channelPricing->getPrice()) {
                $price = $channelPricing->getPrice() / 100;
                if ($channelPricing->getOriginalPrice()) {
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
        if ($product->getDescription()) {
            $productData['description'] = $product->getDescription();
        }
        $mainImage = $product->getImagesByType('main')->first();
        if ($mainImage && $mainImage->getPath()) {
            /** @var ImageInterface $mainImage */
            $productData['image'] = $this->imagineFilterService->getUrlOfFilteredImage(
                $mainImage->getPath(),
                'sylius_shop_product_thumbnail'
            );
        }
        if ($originalPrice) {
            $productData['list_price'] = $originalPrice;
        }
        foreach ($product->getAttributes() as $attribute) {
            $productData[$attribute->getCode()] = $attribute->getValue();
        }

        return $productData;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProductInterface;
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
