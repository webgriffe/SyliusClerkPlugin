<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\ProductVariantNormalizerEvent;
use Webmozart\Assert\Assert;

/**
 * @psalm-import-type clerkIoProductData from ProductVariantNormalizerEvent
 */
trait CommonProductNormalizerTrait
{
    abstract private function getImageFilterToApply(): string;

    abstract private function getFallbackLocale(): string;

    abstract private function getUrlGenerator(): UrlGeneratorInterface;

    abstract private function getCacheManager(): CacheManager;

    abstract private function getImageType(): string;

    private function getMainImage(ProductInterface $product, ?ProductVariantInterface $productVariant = null): ?ProductImageInterface
    {
        if ($productVariant !== null) {
            $variantImageByType = $productVariant->getImagesByType($this->getImageType())->first();
            if ($variantImageByType instanceof ProductImageInterface) {
                return $variantImageByType;
            }
            $variantImage = $productVariant->getImages()->first();
            if ($variantImage instanceof ProductImageInterface) {
                return $variantImage;
            }
        }
        $imageByType = $product->getImagesByType($this->getImageType())->first();
        if ($imageByType instanceof ProductImageInterface) {
            return $imageByType;
        }
        $image = $product->getImages()->first();
        if ($image instanceof ProductImageInterface) {
            return $image;
        }

        return null;
    }

    private function getUrlOfImage(ProductImageInterface $productMainImage, ChannelInterface $channel): string
    {
        $channelRequestContext = $this->getUrlGenerator()->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::stringNotEmpty($channelHost);
        $channelRequestContext->setHost($channelHost);

        $imagePath = $productMainImage->getPath();
        Assert::stringNotEmpty($imagePath, 'Product image path must not be empty.');

        $imageUrl = $this->getCacheManager()->getBrowserPath(
            $imagePath,
            $this->getImageFilterToApply(),
        );

        $channelRequestContext->setHost($previousHost);

        return $imageUrl;
    }

    private function getUrlOfProduct(ProductTranslationInterface $productTranslation, ChannelInterface $channel): string
    {
        $channelRequestContext = $this->getUrlGenerator()->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);

        $url = $this->getUrlGenerator()->generate(
            'sylius_shop_product_show',
            [
                'slug' => $productTranslation->getSlug(),
                '_locale' => $productTranslation->getLocale(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $channelRequestContext->setHost($previousHost);

        return $url;
    }

    /**
     * @return array<int|string>
     */
    private function getCategoryIds(ProductInterface $product, ChannelInterface $channel): array
    {
        $treeRoot = $channel->getMenuTaxon();
        $categoryIds = [];
        foreach ($product->getTaxons() as $taxon) {
            $taxonId = $taxon->getId();
            if (!is_string($taxonId) && !is_int($taxonId)) {
                throw new \InvalidArgumentException('Taxon ID must be a string or an integer, "' . gettype($taxonId) . '" given.');
            }
            if ($treeRoot !== null && $taxon->getRoot() !== $treeRoot) {
                continue;
            }
            $categoryIds[] = $taxonId;
        }

        return $categoryIds;
    }

    /**
     * @psalm-suppress InvalidReturnType
     *
     * @param clerkIoProductData $productData
     *
     * @return clerkIoProductData
     */
    private function enrichProductDataFromProduct(array $productData, ProductInterface $product, ChannelInterface $channel, string $localeCode): array
    {
        foreach ($product->getAttributesByLocale($localeCode, $this->getFallbackLocale()) as $attribute) {
            /** @var bool|string|int|float|\DateTimeInterface|array|null $value */
            $value = $attribute->getValue();
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            }
            $productData['attribute_' . (string) $attribute->getCode()] = $value;
        }
        /** @var array<string, string[]> $optionWithValues */
        $optionWithValues = [];
        foreach ($product->getEnabledVariants() as $productVariant) {
            foreach ($productVariant->getOptionValues() as $optionValue) {
                $productOption = $optionValue->getOption();
                if (null === $productOption) {
                    continue;
                }
                $optionWithValues[(string) $productOption->getCode()][] = (string) $optionValue->getValue();
            }
        }
        foreach ($optionWithValues as $optionCode => $optionValues) {
            $productData['product_option_' . $optionCode] = $optionValues;
        }
        $productData['product_code'] = $product->getCode();
        $productData['is_simple'] = $product->isSimple();
        $productData['variant_selection_method'] = $product->getVariantSelectionMethod();

        return $productData; //@phpstan-ignore-line
    }

    /**
     * @param clerkIoProductData $productData
     *
     * @return clerkIoProductData
     */
    private function enrichProductDataFromProductTranslation(array $productData, ProductTranslationInterface $productTranslation): array
    {
        $productData['slug'] = $productTranslation->getSlug();
        $productData['short_description'] = $productTranslation->getShortDescription();
        $productData['meta_keywords'] = $productTranslation->getMetaKeywords();
        $productData['meta_description'] = $productTranslation->getMetaDescription();

        return $productData; //@phpstan-ignore-line
    }

    /**
     * @param clerkIoProductData $productData
     *
     * @return clerkIoProductData
     */
    private function enrichProductDataFromProductVariant(array $productData, ProductVariantInterface $productVariant, ChannelInterface $channel): array
    {
        $productData['variant_code'] = $productVariant->getCode();
        $productData['depth'] = $productVariant->getDepth();
        $productData['weight'] = $productVariant->getWeight();
        $productData['height'] = $productVariant->getHeight();
        $productData['width'] = $productVariant->getWidth();
        $productData['on_hand'] = $productVariant->getOnHand();
        $productData['on_hold'] = $productVariant->getOnHold();
        $productData['version'] = $productVariant->getVersion();
        foreach ($productVariant->getOptionValues() as $optionValue) {
            $productOption = $optionValue->getOption();
            if (null === $productOption) {
                continue;
            }
            $productData['variant_option_' . (string) $productOption->getCode()] = $optionValue->getValue();
        }
        /** @var CatalogPromotionInterface $promotion */
        foreach ($productVariant->getAppliedPromotionsForChannel($channel) as $promotion) {
            $productData['variant_promotion_' . (string) $promotion->getCode()] = $promotion->getName();
        }

        return $productData; //@phpstan-ignore-line
    }
}
