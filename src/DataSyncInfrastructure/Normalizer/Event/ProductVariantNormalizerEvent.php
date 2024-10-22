<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantNormalizerEvent
{
    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      description?: string,
     *      price: float,
     *      list_price: float,
     *      image?: string,
     *      url: string,
     *      categories: array<int|string>,
     *      created_at: string,
     *  }&array<string, mixed> $productData
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
     * @return array{
     *      id: string|int,
     *      name: string,
     *      description?: string,
     *      price: float,
     *      list_price: float,
     *      image?: string,
     *      url: string,
     *      categories: array<int|string>,
     *      created_at: string,
     *  }&array<string, mixed>
     */
    public function getProductData(): array
    {
        return $this->productData;
    }

    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      description?: string,
     *      price: float,
     *      list_price: float,
     *      image?: string,
     *      url: string,
     *      categories: array<int|string>,
     *      created_at: string,
     *  }&array<string, mixed> $productData
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
