<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webmozart\Assert\Assert;

class FeedController extends Controller
{
    /**
     * @var FeedGenerator
     */
    private $feedGenerator;

    public function __construct(FeedGenerator $productsFeedGenerator)
    {
        $this->feedGenerator = $productsFeedGenerator;
    }

    public function feedAction(int $channelId): Response
    {
        $channel = $this->getChannel($channelId);
        Assert::isInstanceOf($channel->getDefaultLocale(), LocaleInterface::class);

        return new JsonResponse($this->feedGenerator->generate($channel), Response::HTTP_OK, [], true);
    }

    /**
     * @param int $channelId
     *
     * @return ChannelInterface
     */
    private function getChannel(int $channelId): ChannelInterface
    {
        $channel = $this->get('sylius.repository.channel')->find($channelId);
        if (!$channel) {
            throw new NotFoundHttpException('Cannot find channel with ID ' . $channelId);
        }

        return $channel;
    }
}
