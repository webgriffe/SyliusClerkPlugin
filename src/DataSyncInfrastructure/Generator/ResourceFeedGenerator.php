<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\ResourceProviderInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\ValueObject\Feed;

final readonly class ResourceFeedGenerator implements FeedGeneratorInterface
{
    private Resource $resource;

    public function __construct(
        private ResourceProviderInterface $resourceProvider,
        private NormalizerInterface&EncoderInterface $serializer,
        private LoggerInterface $logger,
        Resource|string $resource,
    ) {
        // This is needed for Sf 5 compatibility
        if (is_string($resource)) {
            $resource = Resource::from($resource);
        }
        $this->resource = $resource;
    }

    public function generate(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): Feed {
        /** @var object[] $feedObjects */
        $feedObjects = $this->resourceProvider->provide($channel, $localeCode, $modifiedAfter, $limit, $offset);

        $payload = [];
        foreach ($feedObjects as $feedObject) {
            try {
                $payload[] = $this->serializer->normalize(
                    $feedObject,
                    'array',
                    [
                        'type' => 'webgriffe_sylius_clerk_plugin',
                        'channel' => $channel,
                        'localeCode' => $localeCode,
                    ],
                );
            } catch (\Throwable $e) {
                $this->logger->error(
                    sprintf(
                        'An error occurred while normalizing an object for the %s feed: %s',
                        $this->resource->value,
                        $e->getMessage(),
                    ),
                    ['exception' => $e],
                );

                continue;
            }
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
