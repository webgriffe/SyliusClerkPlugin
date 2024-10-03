<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Unit\DataSyncInfrastructure\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Model\QueryBuilderInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Provider\QueryBuilderResourceProvider;

final class QueryBuilderResourceProviderTest extends TestCase
{
    private QueryBuilderResourceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new QueryBuilderResourceProvider(new TestQueryBuilder());
    }

    public function testItProvidesDataFromQueryBuilder(): void
    {
        $result = $this->provider->provide(new Channel(), 'en_US');

        $this->assertIsArray($result);
        $this->assertEquals(['test1', 'test2'], $result);
    }
}

final class TestQueryBuilder implements QueryBuilderInterface
{
    public function getResult(
        ChannelInterface $channel,
        string $localeCode,
        ?\DateTimeInterface $modifiedAfter = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return [
            'test1',
            'test2',
        ];
    }

    public function getResource(): Resource
    {
        return Resource::PRODUCTS;
    }
}
