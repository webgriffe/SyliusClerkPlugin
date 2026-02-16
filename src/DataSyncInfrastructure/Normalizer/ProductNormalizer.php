<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\ProductVariantNormalizerEvent;
use Webmozart\Assert\Assert;

/**
 * @psalm-import-type clerkIoProductData from ProductVariantNormalizerEvent
 */
final readonly class ProductNormalizer implements NormalizerInterface
{
    use CommonProductNormalizerTrait;

    public function __construct(
        private ProductVariantResolverInterface $productVariantResolver,
        private EventDispatcherInterface $eventDispatcher,
        private ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        private UrlGeneratorInterface $urlGenerator,
        private CacheManager $cacheManager,
        private string $imageType,
        private string $imageFilterToApply,
        private string $fallbackLocale,
    ) {
    }

    /**
     * @param ProductInterface|mixed $object
     *
     * @return clerkIoProductData
     */
    #[\Override]
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $product = $object;
        Assert::isInstanceOf($product, ProductInterface::class);
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The given context should contain a ChannelInterface instance.');
        $localeCode = $context['localeCode'];
        Assert::stringNotEmpty($localeCode, 'The given context should contain a non-empty string localeCode.');

        $productTranslation = $product->getTranslation($localeCode);

        $productId = $product->getId();
        if (!is_string($productId) && !is_int($productId)) {
            throw new \InvalidArgumentException('Product ID must be a string or an integer, "' . gettype($productId) . '" given.');
        }

        $productName = $productTranslation->getName();
        if ($productName === null) {
            throw new \InvalidArgumentException('Product name must not be null for the product "' . $productId . '".');
        }
        $productDescription = $productTranslation->getDescription();
        $productVariant = $this->productVariantResolver->getVariant($product);
        if (!$productVariant instanceof ProductVariantInterface) {
            throw new \InvalidArgumentException('At least one product variant should exists for the product "' . $productId . '".');
        }
        $price = null;
        $originalPrice = null;

        try {
            $price = $this->productVariantPricesCalculator->calculate($productVariant, ['channel' => $channel]);
            $originalPrice = $this->productVariantPricesCalculator->calculateOriginal($productVariant, ['channel' => $channel]);
        } catch (MissingChannelConfigurationException) {
        }

        $productMainImage = $this->getMainImage($product);
        $productMainImageUrl = null;
        if ($productMainImage !== null) {
            $productMainImageUrl = $this->getUrlOfImage($productMainImage, $channel);
        }
        $productUrl = $this->getUrlOfProduct($productTranslation, $channel);
        $createdAt = $product->getCreatedAt();
        if ($createdAt === null) {
            throw new \InvalidArgumentException('Product created at date must not be null for the product "' . $productId . '".');
        }

        $productData = [
            'id' => $productId,
            'name' => $productName,
            'price' => $price !== null ? $price / 100 : null,
            'list_price' => $originalPrice !== null ? $originalPrice / 100 : null,
            'url' => $productUrl,
            'categories' => $this->getCategoryIds($product, $channel),
            'created_at' => $createdAt->format('c'),
        ];
        if ($productDescription !== null) {
            $productData['description'] = $productDescription;
        }
        if ($productMainImageUrl !== null) {
            $productData['image'] = $productMainImageUrl;
        }
        $productData = $this->enrichProductDataFromProduct($productData, $product, $channel, $localeCode);
        $productData = $this->enrichProductDataFromProductTranslation($productData, $productTranslation);
        $productData = $this->enrichProductDataFromProductVariant($productData, $productVariant, $channel);

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

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ProductInterface &&
            $format === 'array' &&
            array_key_exists('type', $context) &&
            $context['type'] === 'webgriffe_sylius_clerk_plugin'
        ;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            ProductInterface::class => true,
        ];
    }

    #[\Override]
    public function getImageType(): string
    {
        return $this->imageType;
    }

    #[\Override]
    private function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    #[\Override]
    private function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }

    #[\Override]
    public function getImageFilterToApply(): string
    {
        return $this->imageFilterToApply;
    }

    #[\Override]
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }
}
