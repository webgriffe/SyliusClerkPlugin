<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class FeedController extends Controller
{
    public function feedAction(int $channelId): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->find($channelId);
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);
        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->get('sylius.repository.product');
        $queryBuilder = $productRepository
            ->createListQueryBuilder($channel->getDefaultLocale()->getCode())
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
        ;

        return new Response(
            json_encode(
                [
                    'products' => array_map(
                        function (ProductInterface $product) {
                            return $product->getName();
                        },
                        $queryBuilder->getQuery()->getResult()
                    ),
                ]
            )
        );
    }
}
