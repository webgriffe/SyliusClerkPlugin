<?php
declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller
{
    public function feedAction(int $channelId): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->get('sylius.repository.channel')->find($channelId);
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
                    )
                ]
            )
        );
    }
}
