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

    /** @var QueryBuilderFactoryInterface */
    private $productsQueryBuilderFactory;

    /** @var QueryBuilderFactoryInterface */
    private $taxonsQueryBuilderFactory;

    /** @var QueryBuilderFactoryInterface */
    private $ordersQueryBuilderFactory;

    /** @var NormalizerInterface&EncoderInterface */
    private $serializer;

    /**
     * @param NormalizerInterface&EncoderInterface $serializer
     */
    public function __construct(
        QueryBuilderFactoryInterface $productsQueryBuilderFactory,
        QueryBuilderFactoryInterface $taxonsQueryBuilderFactory,
        QueryBuilderFactoryInterface $ordersQueryBuilderFactory,
        $serializer
    ) {
        $this->productsQueryBuilderFactory = $productsQueryBuilderFactory;
        $this->taxonsQueryBuilderFactory = $taxonsQueryBuilderFactory;
        $this->ordersQueryBuilderFactory = $ordersQueryBuilderFactory;
        $this->serializer = $serializer;
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
        /** @var ProductInterface $product */
        foreach ($productsQueryBuilder->getQuery()->getResult() as $product) {
            $feed['products'][] = $product;
        }
        /** @var TaxonInterface $taxon */
        foreach ($taxonsQueryBuilder->getQuery()->getResult() as $taxon) {
            $feed['categories'][] = $taxon;
        }
        /** @var OrderInterface $order */
        foreach ($ordersQueryBuilder->getQuery()->getResult() as $order) {
            $feed['sales'][] = $order;
        }

        return (string) $this->serializer->encode(
            $this->serializer->normalize($feed, self::NORMALIZATION_FORMAT, ['channel' => $channel]),
            'json'
        );
    }
}
