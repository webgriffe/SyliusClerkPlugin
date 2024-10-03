<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Integration\DataSyncInfrastructure\Doctrine\ORM;

use Fidry\AliceDataFixtures\Loader\PurgerLoader;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\ChannelBundle\Doctrine\ORM\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Webgriffe\SyliusAkeneoPlugin\DataFixtures\DataFixture;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Doctrine\ORM\ProductsQueryBuilder;

final class ProductsQueryBuilderTest extends KernelTestCase
{
    private ProductsQueryBuilder $queryBuilder;

    private ChannelRepository $channelRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBuilder = self::getContainer()->get('webgriffe_sylius_clerk_plugin.query_builder.products');
        $this->channelRepository = self::getContainer()->get('sylius.repository.channel');
        /** @var PurgerLoader $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $ORMResourceFixturePath = __DIR__ . '/fixtures/ProductsQueryBuilderTest/' . $this->getName() . '.yaml';
        $fixtureLoader->load(
            [$ORMResourceFixturePath],
            [],
            [],
            PurgeMode::createDeleteMode(),
        );
    }

    public function testItQueriesProducts(): void
    {
        $channel = $this->channelRepository->findOneByCode('DEFAULT');
        $products = $this->queryBuilder->getResult($channel, 'en_US');

        $this->assertIsArray($products);
        $this->assertCount(1, $products);
        $product = $products[0];
        $this->assertEquals('STAR_WARS_TSHIRT_M', $product->getCode());
    }
}
