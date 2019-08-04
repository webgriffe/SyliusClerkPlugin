<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Integration\Service;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

class FeedGeneratorTest extends KernelTestCase
{
    public function setUp()
    {
        $kernel = self::bootKernel();
        $fixtureLoader = $kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load(
            [
                __DIR__ . '/../DataFixtures/ORM/resources/channel_default.yml',
                __DIR__ . '/../DataFixtures/ORM/resources/taxons.yml',
                __DIR__ . '/../DataFixtures/ORM/resources/products.yml',
            ],
            [],
            [],
            PurgeMode::createDeleteMode()
        );
    }

    /**
     * @test
     */
    public function it_generates_products_feed()
    {
        self::bootKernel();
        $generator = self::$container->get(FeedGenerator::class);
        $channelRepository = self::$container->get('sylius.repository.channel');

        $feed = $generator->generate($channelRepository->findOneByCode('DEFAULT'));
        $this->assertInternalType('string', $feed);
        $decodedFeed = json_decode($feed, true);
        $this->assertCount(10, $decodedFeed['categories']);
        $this->assertCount(10, $decodedFeed['products']);
    }
}
