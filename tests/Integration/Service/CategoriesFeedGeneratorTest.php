<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Integration\Service;

use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webgriffe\SyliusClerkPlugin\Service\CategoriesFeedGenerator;

class CategoriesFeedGeneratorTest extends KernelTestCase
{
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $fixtureLoader = $kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load(
            [
                __DIR__ . '/../DataFixtures/ORM/resources/channel_default.yml',
                __DIR__ . '/../DataFixtures/ORM/resources/taxons.yml'
            ],
            [],
            [],
            PurgeMode::createDeleteMode()
        );
    }

    /**
     * @test
     */
    public function it_generates_categories_feed()
    {
        $generator = self::$container->get(CategoriesFeedGenerator::class);
        $channelRepository = self::$container->get('sylius.repository.channel');

        $feed = $generator->generate($channelRepository->findOneByCode('DEFAULT'));
        $this->assertInternalType('string', $feed);
        $decodedFeed = json_decode($feed, false);
        $this->assertCount(10, $decodedFeed);
    }
}
