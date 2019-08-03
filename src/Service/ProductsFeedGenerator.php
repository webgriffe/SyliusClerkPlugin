<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

class ProductsFeedGenerator
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ProductRepositoryInterface $productRepository, SerializerInterface $serializer)
    {
        $this->productRepository = $productRepository;
        $this->serializer = $serializer;
    }

    public function generate(ChannelInterface $channel): string
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
            $productsData[] = $product;
        }

        return $this->serializer->serialize($productsData, 'json', ['channel' => $channel]);
    }
}
