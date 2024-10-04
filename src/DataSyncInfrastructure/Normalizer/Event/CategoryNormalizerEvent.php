<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class CategoryNormalizerEvent
{
    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      url: string,
     *      subcategories: array<int|string>,
     *      image?: string,
     *      description?: string,
     *  } $categoryData
     */
    public function __construct(
        private array $categoryData,
        private readonly TaxonInterface $taxon,
        private readonly ChannelInterface $channel,
        private readonly string $localeCode,
        private readonly array $context,
    ) {
    }

    /**
     * @return array{
     *      id: string|int,
     *      name: string,
     *      url: string,
     *      subcategories: array<int|string>,
     *      image?: string,
     *      description?: string,
     *  }
     */
    public function getCategoryData(): array
    {
        return $this->categoryData;
    }

    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      url: string,
     *      subcategories: array<int|string>,
     *      image?: string,
     *      description?: string,
     *  } $categoryData
     */
    public function setCategoryData(array $categoryData): void
    {
        $this->categoryData = $categoryData;
    }

    public function getTaxon(): TaxonInterface
    {
        return $this->taxon;
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
