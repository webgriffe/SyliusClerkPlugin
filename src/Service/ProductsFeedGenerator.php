<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class ProductsFeedGenerator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var ProductVariantResolverInterface
     */
    private $productVariantResolver;
    /**
     * @var FilterService
     */
    private $imagineFilterService;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductVariantResolverInterface $productVariantResolver,
        FilterService $imagineFilterService,
        RouterInterface $router
    ) {
        $this->productRepository = $productRepository;
        $this->productVariantResolver = $productVariantResolver;
        $this->imagineFilterService = $imagineFilterService;
        $this->router = $router;
    }

    public function generate(ChannelInterface $channel): array
    {
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @noinspection NullPointerExceptionInspection */
        $localeCode = $channel->getDefaultLocale()->getCode();
        $queryBuilder = $this->productRepository
            ->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
        ;

        $productsData = [];
        /** @var ProductInterface $product */
        foreach ($queryBuilder->getQuery()->getResult() as $product) {
            $productDefaultVariant = $this->productVariantResolver->getVariant($product);
            if (!$productDefaultVariant) { // TODO test this
                continue;
            }
            /** @var ProductVariantInterface $productDefaultVariant */
            Assert::isInstanceOf($productDefaultVariant, ProductVariantInterface::class);
            $channelPricing = $productDefaultVariant->getChannelPricingForChannel($channel);
            if (!$channelPricing) { // TODO test this
                continue;
            }

            $productData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'sku' => $product->getCode(),
                'price' => $channelPricing->getPrice() / 100,
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
            if ($channelPricing->getOriginalPrice()) { // todo test this
                $productData['list_price'] = $channelPricing->getOriginalPrice() / 100;
            }

            $productsData[] = $productData;
        }

        return $productsData;
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    private function getTaxonsIds(ProductInterface $product): array
    {
        $taxonsIds = [];
        foreach ($product->getTaxons() as $taxon) {
            $taxonsIds[] = $taxon->getId();
        }

        return $taxonsIds;
    }
}
