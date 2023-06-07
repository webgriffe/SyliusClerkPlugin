<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface;
use Webgriffe\SyliusClerkPlugin\Resolver\PageResolverInterface;

final class FeedGenerator implements FeedGeneratorInterface
{
    public const NORMALIZATION_FORMAT = 'clerk_array';

    /** @param NormalizerInterface&EncoderInterface $serializer */
    public function __construct(
        private QueryBuilderFactoryInterface $productsQueryBuilderFactory,
        private QueryBuilderFactoryInterface $taxonsQueryBuilderFactory,
        private QueryBuilderFactoryInterface $ordersQueryBuilderFactory,
        private QueryBuilderFactoryInterface $customersQueryBuilderFactory,
        private PageResolverInterface $pageResolver,
        private $serializer
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function generate(ChannelInterface $channel): string
    {
        $productsQueryBuilder = $this->productsQueryBuilderFactory->createQueryBuilder($channel);
        $taxonsQueryBuilder = $this->taxonsQueryBuilderFactory->createQueryBuilder($channel);
        $ordersQueryBuilder = $this->ordersQueryBuilderFactory->createQueryBuilder($channel);
        $customersQueryBuilder = $this->customersQueryBuilderFactory->createQueryBuilder($channel);

        $feed = [
            'products' => [],
            'categories' => [],
            'customers' => [],
            'sales' => [],
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
        /** @var OrderInterface[] $orders */
        $orders = $ordersQueryBuilder->getQuery()->getResult();
        foreach ($orders as $order) {
            $feed['sales'][] = $order;
        }
        $feed['pages'] = $this->pageResolver->createPageList();

        return $this->serializer->encode(
            $this->serializer->normalize($feed, self::NORMALIZATION_FORMAT, ['channel' => $channel]),
            'json'
        );
    }
}
