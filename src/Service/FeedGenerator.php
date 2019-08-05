<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

class FeedGenerator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;
    /**
     * @var OrderRepositoryInterface|EntityRepository
     */
    private $orderRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        OrderRepositoryInterface $orderRepository,
        SerializerInterface $serializer
    ) {
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->orderRepository = $orderRepository;
        $this->serializer = $serializer;
    }

    public function generate(ChannelInterface $channel): string
    {
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @noinspection NullPointerExceptionInspection */
        $localeCode = $channel->getDefaultLocale()->getCode();
        // TODO move query builders to standalone classes (which can be overridden in projects)
        $productsQueryBuilder = $this->productRepository
            ->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
        ;
        $taxonsQueryBuilder = $this->taxonRepository->createListQueryBuilder();
        Assert::isInstanceOf($this->orderRepository, EntityRepository::class);
        $ordersQueryBuilder = $this->orderRepository
            ->createQueryBuilder('o')
            ->where('o.channel', ':channel')
            ->where('o.checkoutCompletedAt IS NOT NULL')
        ;

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

        return $this->serializer->serialize($feed, 'json', ['channel' => $channel]);
    }
}
