<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Controller;

if (!interface_exists(\Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Repository\RepositoryInterface::class, \Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class);
}
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator\FeedGeneratorInterface;

final class FeedController extends AbstractController implements FeedControllerInterface
{
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly RepositoryInterface $localeRepository,
        private readonly FeedGeneratorInterface $productsFeedGenerator,
        private readonly FeedGeneratorInterface $categoriesFeedGenerator,
        private readonly FeedGeneratorInterface $ordersFeedGenerator,
        private readonly FeedGeneratorInterface $customersFeedGenerator,
        private readonly FeedGeneratorInterface $pagesFeedGenerator,
    ) {
    }

    public function __invoke(
        string $channelCode,
        string $localeCode,
        string $resourceValue,
        Request $request,
    ): Response {
        $channel = $this->channelRepository->findOneByCode($channelCode);
        if (!$channel instanceof ChannelInterface) {
            throw $this->createNotFoundException();
        }
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);
        if (!$locale instanceof LocaleInterface) {
            throw $this->createNotFoundException();
        }
        if (!$channel->hasLocale($locale)) {
            throw $this->createNotFoundException();
        }
        $resource = Resource::tryFrom($resourceValue);
        if ($resource === null) {
            throw $this->createNotFoundException();
        }

        $feedGenerator = match ($resource) {
            Resource::PRODUCTS => $this->productsFeedGenerator,
            Resource::CATEGORIES => $this->categoriesFeedGenerator,
            Resource::ORDERS => $this->ordersFeedGenerator,
            Resource::CUSTOMERS => $this->customersFeedGenerator,
            Resource::PAGES => $this->pagesFeedGenerator,
        };
        $modifiedAfter = null;
        if ($request->query->has('modified_after')) {
            $modifiedAfterHours = $request->query->getInt('modified_after');
            $modifiedAfter = new \DateTimeImmutable("-{$modifiedAfterHours} hours");
        }
        $limit = null;
        if ($request->query->has('limit')) {
            $limit = $request->query->getInt('limit');
        }
        $offset = null;
        if ($request->query->has('offset')) {
            $offset = $request->query->getInt('offset');
        }
        $feed = $feedGenerator->generate(
            $channel,
            $localeCode,
            $modifiedAfter,
            $limit,
            $offset,
        );

        return new Response(
            $feed->getContent(),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json'],
        );
    }
}
