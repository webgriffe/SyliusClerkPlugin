<?php
declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpKernel\Client;
use Webmozart\Assert\Assert;

class ClerkFeedContext implements Context
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @When /^the Clerk crawler hits the data feed URL for the ("([^"]+)" channel)$/
     */
    public function theClerkCrawlerHitsTheDataFeedUrl(ChannelInterface $channel): void
    {
        $this->client->request('GET', '/clerk/feed/' . $channel->getId(), [], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @Then /^a Clerk feed with (products "([^"]+)" and "([^"]+)") should be received$/
     */
    public function aClerkFeedWithProductsAndShouldBeReceived(array $expectedProducts): void
    {
        $response = $this->client->getResponse();
        Assert::eq($response->getStatusCode(), 200);
        $decodedFeed = \json_decode($response->getContent(), true);
        Assert::isArray($decodedFeed);
        Assert::keyExists($decodedFeed, 'products');
        /** @var ProductInterface $expectedProduct */
        foreach ($expectedProducts as $expectedProduct) {
            Assert::true(\in_array($expectedProduct->getName(), $decodedFeed['products'], true));
        }
    }
}
