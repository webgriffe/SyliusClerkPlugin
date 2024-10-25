<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\ProductVariantNormalizerEvent;
use Webmozart\Assert\Assert;

/**
 * @method array getSupportedTypes(?string $format)
 */
final readonly class ProductVariantNormalizer implements NormalizerInterface
{
    use CommonProductNormalizerTrait;

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

        $productMainImage = $this->getMainImage($product, $productVariant);
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

    private function getImageType(): string
    {
        return $this->imageType;
    }

    private function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    private function getCacheManager(): CacheManager
    {
        return $this->cacheManager;
    }
}
