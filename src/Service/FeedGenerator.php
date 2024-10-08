<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface;
use Webgriffe\SyliusClerkPlugin\Resolver\OrderResolverInterface;
use Webgriffe\SyliusClerkPlugin\Resolver\PageResolverInterface;

/**
 * @deprecated This class is deprecated and will be removed in the next major version.
 */
final class FeedGenerator implements FeedGeneratorInterface
{
    public const NORMALIZATION_FORMAT = 'clerk_array';

    /** @param NormalizerInterface&EncoderInterface $serializer */
    public function __construct(
        private QueryBuilderFactoryInterface $productsQueryBuilderFactory,
        private QueryBuilderFactoryInterface $taxonsQueryBuilderFactory,
        private OrderResolverInterface $ordersResolver,
        private QueryBuilderFactoryInterface $customersQueryBuilderFactory,
        private PageResolverInterface $pageResolver,
        private $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function generate(ChannelInterface $channel): string
    {
        $productsQueryBuilder = $this->productsQueryBuilderFactory->createQueryBuilder($channel);
        $taxonsQueryBuilder = $this->taxonsQueryBuilderFactory->createQueryBuilder($channel);
        $customersQueryBuilder = $this->customersQueryBuilderFactory->createQueryBuilder($channel);

        $feed = [
            'products' => [],
            'categories' => [],
            'customers' => [],
            'created' => time(),
            'strict' => false,
        ];
        /** @var ProductInterface[] $products */
        $products = $productsQueryBuilder->getQuery()->getResult();
        foreach ($products as $product) {
            $feed['products'][] = $product;
        }
        /** @var TaxonInterface[] $taxa */
        $taxa = $taxonsQueryBuilder->getQuery()->getResult();
        foreach ($taxa as $taxon) {
            $feed['categories'][] = $taxon;
        }
        /** @var CustomerInterface[] $customers */
        $customers = $customersQueryBuilder->getQuery()->getResult();
        foreach ($customers as $customer) {
            $feed['customers'][] = $customer;
        }
        $feed['sales'] = $this->ordersResolver->createOrdersList($channel);
        $feed['pages'] = $this->pageResolver->createPageList();

        return $this->serializer->encode(
            $this->serializer->normalize($feed, self::NORMALIZATION_FORMAT, ['channel' => $channel]),
            'json',
        );
    }
}
