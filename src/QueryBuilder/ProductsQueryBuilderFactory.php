<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\QueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use query builder event on v2 feed generation instead.
 */
final class ProductsQueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    #[\Override]
    public function createQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @noinspection NullPointerExceptionInspection */
        $localeCode = $channel->getDefaultLocale()->getCode();
        Assert::string($localeCode);

        return $this->productRepository
            ->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel);
    }
}
