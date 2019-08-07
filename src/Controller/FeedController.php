<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webmozart\Assert\Assert;

class FeedController extends AbstractController
{
    /**
     * @var FeedGenerator
     */
    private $feedGenerator;
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;
    /**
     * @var string
     */
    private $privateApiKey;

    public function __construct(
        FeedGenerator $productsFeedGenerator,
        ChannelRepositoryInterface $channelRepository,
        string $privateApiKey
    ) {
        $this->feedGenerator = $productsFeedGenerator;
        $this->channelRepository = $channelRepository;
        $this->privateApiKey = $privateApiKey;
    }

    public function feedAction(int $channelId, Request $request): Response
    {
        if (!$this->isSecurityHashInRequestValid($request)) {
            throw new AccessDeniedHttpException();
        }
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
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if (!$channel) {
            throw new NotFoundHttpException('Cannot find channel with ID ' . $channelId);
        }
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }

    private function isSecurityHashInRequestValid(Request $request): bool
    {
        $salt = $request->query->get('salt');
        $hash = $request->query->get('hash');
        $calculatedHash = hash('sha512', $salt . $this->privateApiKey . floor(time() / 100));

        return $hash === $calculatedHash;
    }
}
