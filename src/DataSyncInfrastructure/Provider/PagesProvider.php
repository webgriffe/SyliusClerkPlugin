<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ResourceProviderInterface<array{
 *     id: int|string,
 *     type: string,
 *     url: string,
 *     title: string,
 *     text: string,
 * }&array<string, mixed>>
 */
final readonly class PagesProvider implements ResourceProviderInterface
{
    /**
     * @param array<array-key, array{
     *     id: string,
     *     type: string,
     *     routeName: string,
     *     routeParameters: array,
     *     title: string,
     *     text: string,
     * }&array<string, mixed>> $pages
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private array $pages,
    ) {
    }

    #[\Override]
    public function provide(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $pagesData = [];
        foreach ($this->pages as $page) {
            $pageData = [
                'url' => $this->getPageUrl($page, $channel, $localeCode),
            ];
            unset($page['routeName'], $page['routeParameters']);
            $pagesData[] = array_merge($page, $pageData);
        }

        return $pagesData;
    }

    /**
     * @param array{
     *      id: string,
     *      type: string,
     *      routeName: string,
     *      routeParameters: array,
     *      title: string,
     *      text: string,
     *  }&array<string, mixed> $page
     */
    public function getPageUrl(array $page, ChannelInterface $channel, string $localeCode): string
    {
        $channelRequestContext = $this->urlGenerator->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);

        $url = $this->urlGenerator->generate(
            $page['routeName'],
            array_merge(['_locale' => $localeCode], $page['routeParameters']),
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $channelRequestContext->setHost($previousHost);

        return $url;
    }
}
