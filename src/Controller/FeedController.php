<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Controller;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webgriffe\SyliusClerkPlugin\Service\FeedGeneratorInterface;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProviderInterface;
use Webmozart\Assert\Assert;

final class FeedController extends AbstractController
{
    /**
     * @var FeedGeneratorInterface
     */
    private $feedGenerator;
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;
    /**
     * @var PrivateApiKeyProviderInterface
     */
    private $privateApiKeyProvider;

    public function __construct(
        FeedGeneratorInterface $productsFeedGenerator,
        ChannelRepositoryInterface $channelRepository,
        PrivateApiKeyProviderInterface $privateApiKeyProvider
    ) {
        $this->feedGenerator = $productsFeedGenerator;
        $this->channelRepository = $channelRepository;
        $this->privateApiKeyProvider = $privateApiKeyProvider;
    }

    public function feedAction(int $channelId, Request $request): Response
    {
        $channel = $this->getChannel($channelId);
        if (!$this->isSecurityHashInRequestValidForChannel($request, $channel)) {
            throw new AccessDeniedHttpException();
        }

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

    private function isSecurityHashInRequestValidForChannel(Request $request, ChannelInterface $channel): bool
    {
        $privateApiKey = $this->privateApiKeyProvider->providePrivateApiKeyForChannel($channel);
        $salt = $request->query->get('salt');
        $hash = $request->query->get('hash');
        $calculatedHash = hash('sha512', $salt . $privateApiKey . floor(time() / 100));

        return $hash === $calculatedHash;
    }
}
