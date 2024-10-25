<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

trait CommonProductNormalizerTrait
{
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

    public function getUrlOfImage(ProductImageInterface $productMainImage, ChannelInterface $channel): string
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
            $this->imageFilterToApply,
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
    private function getCategoryIds(ProductInterface $product): array
    {
        $categoryIds = [];
        foreach ($product->getTaxons() as $taxon) {
            $taxonId = $taxon->getId();
            if (!is_string($taxonId) && !is_int($taxonId)) {
                throw new \InvalidArgumentException('Taxon ID must be a string or an integer, "' . gettype($taxonId) . '" given.');
            }
            $categoryIds[] = $taxonId;
        }

        return $categoryIds;
    }
}
