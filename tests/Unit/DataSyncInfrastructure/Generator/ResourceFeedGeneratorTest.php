<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Unit\DataSyncInfrastructure\Generator;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator\ResourceFeedGenerator;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\ResourceProviderInterface;

final class ResourceFeedGeneratorTest extends TestCase
{
    private ResourceFeedGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $product = new Product();
        $product->setCode('PRODUCT_CODE');
        $resourceProvider = new InMemoryResourceProvider([$product]);
        $this->generator = new ResourceFeedGenerator(
            $resourceProvider,
            new Serializer([new TestProductNormalizer()], [new JsonEncoder()]),
            new Logger('test'),
            Resource::PRODUCTS,
        );
    }

    public function testItGeneratesFeed(): void
    {
        $channel = new Channel();
        $feed = $this->generator->generate($channel, 'en_US');
        $this->assertSame(Resource::PRODUCTS, $feed->getResource());
        $this->assertEquals(
            '[{"code":"PRODUCT_CODE"}]',
            $feed->getContent(),
        );
        $this->assertEquals($channel, $feed->getChannel());
        $this->assertEquals('en_US', $feed->getLocaleCode());
    }
}

final readonly class InMemoryResourceProvider implements ResourceProviderInterface
{
    public function __construct(
        private array $resources = [],
    ) {
    }

    public function provide(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        return $this->resources;
    }
}

final readonly class TestProductNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'code' => $object->getCode(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Product;
    }
}
