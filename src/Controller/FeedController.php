<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Liip\ImagineBundle\Service\FilterService;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class FeedController extends Controller
{
    /**
     * @var FilterService
     */
    private $imagineFilterService;

    /**
     * FeedController constructor.
     */
    public function __construct(FilterService $imagineFilterService)
    {
        $this->imagineFilterService = $imagineFilterService;
    }

    public function feedAction(int $channelId): Response
    {
        $channel = $this->getChannel($channelId);
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');
        /** @noinspection NullPointerExceptionInspection */
        $localeCode = $channel->getDefaultLocale()->getCode();
        $queryBuilder = $productRepository
            ->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
        ;

        $defaultProductVariantResolver = $this->get('sylius.product_variant_resolver.default');

        $productsData = [];
        /** @var ProductInterface $product */
        foreach ($queryBuilder->getQuery()->getResult() as $product) {
            $productDefaultVariant = $defaultProductVariantResolver->getVariant($product);
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
                'url' => $this->generateUrl(
                    'sylius_shop_product_show',
                    ['slug' => $product->getSlug(), '_locale' => $product->getTranslation()->getLocale()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ];
            if ($product->getDescription()) { // todo test this
                $productData['description'] = $product->getDescription();
            }
            $mainImage = $product->getImagesByType('main')->first();
            if ($mainImage && $mainImage->getPath()) { // todo test this
                /** @var ImageInterface $mainImage */
                $productData['image'] = $this->imagineFilterService->getUrlOfFilteredImage(
                    $mainImage->getPath(),
                    'sylius_shop_product_thumbnail'
                );
            }
            if ($channelPricing->getOriginalPrice()) { // todo test this
                $productData['list_price'] = $channelPricing->getOriginalPrice() / 100;
            }
            $productData['categories'] = []; // todo implement/test categories

            $productsData[] = $productData;
        }

        return new Response(json_encode(['products' => $productsData]));
    }

    /**
     * @param int $channelId
     *
     * @return ChannelInterface
     */
    private function getChannel(int $channelId): ChannelInterface
    {
        $channel = $this->get('sylius.repository.channel')->find($channelId);
        if (!$channel) {
            throw new NotFoundHttpException('Cannot find channel with ID ' . $channelId);
        }

        return $channel;
    }
}
