<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\ProductVariantNormalizerEvent;
use Webmozart\Assert\Assert;

final readonly class ProductVariantNormalizer implements NormalizerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private UrlGeneratorInterface $urlGenerator,
        private CacheManager $cacheManager,
        private string $imageType,
        private string $imageFilterToApply,
    ) {
    }

    /**
     * @param ProductVariantInterface|mixed $object
     *
     * @return array{
     *     id: string|int,
     *     name: string,
     *     description?: string,
     *     price: float,
     *     list_price: float,
     *     image?: string,
     *     url: string,
     *     categories: array<int|string>,
     *     created_at: string,
     * }&array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $productVariant = $object;
        Assert::isInstanceOf($productVariant, ProductVariantInterface::class);
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The given context should contain a ChannelInterface instance.');
        $localeCode = $context['localeCode'];
        Assert::stringNotEmpty($localeCode, 'The given context should contain a non-empty string localeCode.');
        $product = $productVariant->getProduct();
        Assert::isInstanceOf($product, ProductInterface::class, 'The product variant should have a product.');

        $productVariantTranslation = $productVariant->getTranslation($localeCode);
        $productTranslation = $product->getTranslation($localeCode);

        $productVariantId = $productVariant->getId();
        if (!is_string($productVariantId) && !is_int($productVariantId)) {
            throw new \InvalidArgumentException('Product variant ID must be a string or an integer, "' . gettype($productVariantId) . '" given.');
        }

        $productVariantName = $productVariantTranslation->getName();
        if ($productVariantName === null) {
            throw new \InvalidArgumentException('Product variant name must not be null for the product variant "' . $productVariantId . '".');
        }
        $productDescription = $productTranslation->getDescription();
        $price = $this->productVariantPricesCalculator->calculate($productVariant, ['channel' => $channel]);
        $originalPrice = $this->productVariantPricesCalculator->calculateOriginal($productVariant, ['channel' => $channel]);

        $productMainImage = $this->getMainImage($productVariant, $product);
        $productMainImageUrl = null;
        if ($productMainImage !== null) {
            $productMainImageUrl = $this->getUrlOfImage($productMainImage, $channel);
        }
        $productUrl = $this->getUrlOfProduct($productTranslation, $channel);
        $createdAt = $productVariant->getCreatedAt();
        if ($createdAt === null) {
            throw new \InvalidArgumentException('Product variant created at date must not be null for the product variant "' . $productVariantId . '".');
        }

        $productData = [
            'id' => $productVariantId,
            'name' => $productVariantName,
            'price' => $price / 100,
            'list_price' => $originalPrice / 100,
            'url' => $productUrl,
            'categories' => $this->getCategoryIds($product),
            'created_at' => $createdAt->format('c'),
        ];
        if ($productDescription !== null) {
            $productData['description'] = $productDescription;
        }
        if ($productMainImageUrl !== null) {
            $productData['image'] = $productMainImageUrl;
        }

        $productNormalizerEvent = new ProductVariantNormalizerEvent(
            $productData,
            $productVariant,
            $product,
            $channel,
            $localeCode,
            $context,
        );
        $this->eventDispatcher->dispatch($productNormalizerEvent);

        return $productNormalizerEvent->getProductData();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ProductVariantInterface &&
            $format === 'array' &&
            array_key_exists('type', $context) &&
            $context['type'] === 'webgriffe_sylius_clerk_plugin'
        ;
    }

    public function getMainImage(ProductVariantInterface $productVariant, ProductInterface $product): ?ProductImageInterface
    {
        $variantImageByType = $productVariant->getImagesByType($this->imageType)->first();
        if ($variantImageByType instanceof ProductImageInterface) {
            return $variantImageByType;
        }
        $variantImage = $productVariant->getImages()->first();
        if ($variantImage instanceof ProductImageInterface) {
            return $variantImage;
        }
        $imageByType = $product->getImagesByType($this->imageType)->first();
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
        $channelRequestContext = $this->urlGenerator->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::stringNotEmpty($channelHost);
        $channelRequestContext->setHost($channelHost);

        $imagePath = $productMainImage->getPath();
        Assert::stringNotEmpty($imagePath, 'Product image path must not be empty.');

        $imageUrl = $this->cacheManager->getBrowserPath(
            $imagePath,
            $this->imageFilterToApply,
        );

        $channelRequestContext->setHost($previousHost);

        return $imageUrl;
    }

    private function getUrlOfProduct(ProductTranslationInterface $productTranslation, ChannelInterface $channel): string
    {
        $channelRequestContext = $this->urlGenerator->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);

        $url = $this->urlGenerator->generate(
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
