<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Unit\DataSyncInfrastructure\ValueObject;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\ValueObject\Feed;

final class FeedTest extends TestCase
{
    private Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channel = new Channel();
        $this->channel->setCode('CHANNEL_CODE');
    }

    public function testItFailsIfChannelCodeIsNotSetWhileGeneratingFileName(): void
    {
        $this->channel->setCode(null);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Channel code must be set.');

        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US');
        $feed->getFileName();
    }

    public function testItGeneratesFileNameWithoutAnyOptionalParameter(): void
    {
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US');

        $this->assertSame('CHANNEL_CODE/en_US/products/all.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithModifiedAfter(): void
    {
        $modifiedAfter = new \DateTime('2024-10-03 15:50:00', new \DateTimeZone('Europe/Rome'));
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', $modifiedAfter);

        $this->assertSame('CHANNEL_CODE/en_US/products/modified_after_2024-10-03T15:50:00+02:00.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithLimit(): void
    {
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', null, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/to_10.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithOffset(): void
    {
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', null, null, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/from_10.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithModifiedAfterAndLimit(): void
    {
        $modifiedAfter = new \DateTime('2024-10-03 15:50:00', new \DateTimeZone('Europe/Rome'));
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', $modifiedAfter, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/modified_after_2024-10-03T15:50:00+02:00_to_10.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithModifiedAfterAndOffset(): void
    {
        $modifiedAfter = new \DateTime('2024-10-03 15:50:00', new \DateTimeZone('Europe/Rome'));
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', $modifiedAfter, null, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/modified_after_2024-10-03T15:50:00+02:00_from_10.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithLimitAndOffset(): void
    {
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', null, 10, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/from_10_to_20.json', $feed->getFileName());
    }

    public function testItGeneratesFileNameWithModifiedAfterLimitAndOffset(): void
    {
        $modifiedAfter = new \DateTime('2024-10-03 15:50:00', new \DateTimeZone('Europe/Rome'));
        $feed = new Feed(Resource::PRODUCTS, 'content', $this->channel, 'en_US', $modifiedAfter, 10, 10);

        $this->assertSame('CHANNEL_CODE/en_US/products/modified_after_2024-10-03T15:50:00+02:00_from_10_to_20.json', $feed->getFileName());
    }
}
