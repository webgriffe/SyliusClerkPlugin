<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @psalm-type clerkIoProductData=array{
 *     id: string|int,
 *     name: string,
 *     description?: string,
 *     price: ?float,
 *     list_price: ?float,
 *     image?: string,
 *     url: string,
 *     categories: array<int|string>,
 *     created_at: string,
 * }&array<string, bool|string|int|float|array|null>
 */
final class ProductVariantNormalizerEvent
{
    /**
     * @param clerkIoProductData $productData
     */
    public function __construct(
        private array $productData,
        private readonly ProductVariantInterface $productVariant,
        private readonly ProductInterface $product,
        private readonly ChannelInterface $channel,
        private readonly string $localeCode,
        private readonly array $context,
    ) {
    }

    /**
     * @return clerkIoProductData
     */
    public function getProductData(): array
    {
        return $this->productData;
    }

    /**
     * @param clerkIoProductData $productData
     */
    public function setProductData(array $productData): void
    {
        $this->productData = $productData;
    }

    public function getProductVariant(): ProductVariantInterface
    {
        return $this->productVariant;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
