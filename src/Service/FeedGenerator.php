<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
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
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        TaxonRepositoryInterface $taxonRepository,
        SerializerInterface $serializer
    ) {
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->serializer = $serializer;
    }

    public function generate(ChannelInterface $channel): string
    {
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @noinspection NullPointerExceptionInspection */
        $localeCode = $channel->getDefaultLocale()->getCode();
        $productsQueryBuilder = $this->productRepository
            ->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
        ;
        $taxonsQueryBuilder = $this->taxonRepository->createListQueryBuilder();

        $feed = [
            'products' => [],
            'categories' => [],
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

        return $this->serializer->serialize($feed, 'json', ['channel' => $channel]);
    }
}
