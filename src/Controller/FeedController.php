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
use Webgriffe\SyliusClerkPlugin\Service\ChannelApiKeyCheckerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGeneratorInterface;
use Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProviderInterface;

final class FeedController extends AbstractController
{
    /** @var FeedGeneratorInterface */
    private $feedGenerator;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var PrivateApiKeyProviderInterface */
    private $privateApiKeyProvider;

    private ChannelApiKeyCheckerInterface $channelApiKeyChecker;

    public function __construct(
        FeedGeneratorInterface $productsFeedGenerator,
        ChannelRepositoryInterface $channelRepository,
        PrivateApiKeyProviderInterface $privateApiKeyProvider,
        ChannelApiKeyCheckerInterface $channelApiKeyChecker
    ) {
        $this->feedGenerator = $productsFeedGenerator;
        $this->channelRepository = $channelRepository;
        $this->privateApiKeyProvider = $privateApiKeyProvider;
        $this->channelApiKeyChecker = $channelApiKeyChecker;
    }

    public function feedAction(int $channelId, Request $request): Response
    {
        $channel = $this->getChannel($channelId);
        if (!$this->channelApiKeyChecker->check($channel)) {
            throw new NotFoundHttpException();
        }
        if (!$this->isSecurityHashInRequestValidForChannel($request, $channel)) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse($this->feedGenerator->generate($channel), Response::HTTP_OK, [], true);
    }

    private function getChannel(int $channelId): ChannelInterface
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->find($channelId);
        if ($channel === null) {
            throw new NotFoundHttpException('Cannot find channel with ID ' . $channelId);
        }

        return $channel;
    }

    private function isSecurityHashInRequestValidForChannel(Request $request, ChannelInterface $channel): bool
    {
        $privateApiKey = $this->privateApiKeyProvider->providePrivateApiKeyForChannel($channel);
        $salt = $request->query->get('salt');
        $hash = $request->query->get('hash');
        if ($salt === null) {
            return false;
        }
        $calculatedHash = hash('sha512', $salt . $privateApiKey . floor(time() / 100));

        return $hash === $calculatedHash;
    }
}
