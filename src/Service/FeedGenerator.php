<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface;

final class FeedGenerator implements FeedGeneratorInterface
{
    public const NORMALIZATION_FORMAT = 'clerk_array';

    /** @param NormalizerInterface&EncoderInterface $serializer */
    public function __construct(
        private QueryBuilderFactoryInterface $productsQueryBuilderFactory,
        private QueryBuilderFactoryInterface $taxonsQueryBuilderFactory,
        private QueryBuilderFactoryInterface $ordersQueryBuilderFactory,
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

        $feed = [
            'products' => [],
            'categories' => [],
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
        /** @var OrderInterface[] $orders */
        $orders = $ordersQueryBuilder->getQuery()->getResult();
        foreach ($orders as $order) {
            $feed['sales'][] = $order;
        }

        return $this->serializer->encode(
            $this->serializer->normalize($feed, self::NORMALIZATION_FORMAT, ['channel' => $channel]),
            'json'
        );
    }
}
