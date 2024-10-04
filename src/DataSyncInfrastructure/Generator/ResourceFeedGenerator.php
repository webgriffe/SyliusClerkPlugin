<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator;

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\ResourceProviderInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\ValueObject\Feed;

final readonly class ResourceFeedGenerator implements FeedGeneratorInterface
{
    public function __construct(
        private ResourceProviderInterface $resourceProvider,
        private NormalizerInterface&EncoderInterface $serializer,
        private Resource $resource,
    ) {
    }

    public function generate(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): Feed {
        /** @var object[] $resources */
        $resources = $this->resourceProvider->provide($channel, $localeCode, $modifiedAfter, $limit, $offset);

        $payload = [];
        foreach ($resources as $resource) {
            $payload[] = $this->serializer->normalize(
                $resource,
                'array',
                [
                    'type' => 'webgriffe_sylius_clerk_plugin',
                    'channel' => $channel,
                    'localeCode' => $localeCode,
                ],
            );
        }

        return new Feed(
            resource: $this->resource,
            content: $this->serializer->encode($payload, 'json'),
            channel: $channel,
            localeCode: $localeCode,
            modifiedAfter: $modifiedAfter,
            limit: $limit,
            offset: $offset,
        );
    }
}
