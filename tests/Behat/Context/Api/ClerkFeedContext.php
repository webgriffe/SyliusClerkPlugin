<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Flow\JSONPath\JSONPath;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class ClerkFeedContext implements Context
{
    private array $privateApiKeysForChannels;

    public function __construct(
        private readonly HttpKernelBrowser $client,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @When /^the Clerk crawler hits the data feed URL for the ("[^"]+" channel) in the ("[^"]+" locale) and for the "([^"]+)" resource$/
     */
    public function theClerkCrawlerHitsTheDataFeedURLForTheChannelInTheLocaleAndForTheResource(
        ChannelInterface $channel,
        string $localeCode,
        string $resourceValue,
    ): void {
        $url = $this->router->generate(
            'webgriffe_sylius_clerk_plugin_feed',
            [
                'channelCode' => $channel->getCode(),
                'localeCode' => $localeCode,
                'resourceValue' => $resourceValue,
            ],
        );
        $this->client->request(
            'GET',
            $url,
            [],
            [],
            ['ACCEPT' => 'application/json'],
        );
    }

    /**
     * @Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
     */
    public function theClerkCrawlerShouldReceiveASuccessfulHttpResponseWithAValidJsonFeedAsItsContent(): void
    {
        Assert::eq($this->client->getResponse()->getStatusCode(), 200);
        Assert::eq($this->client->getResponse()->headers->get('Content-Type'), 'application/json');
        Assert::isArray(json_decode($this->client->getResponse()->getContent(), true));
    }

    /**
     * @Transform /^in this feed JSON paths? "([^"]*)"$/
     */
    public function transformJsonPath(string $jsonPath): JSONPath
    {
        $responseFeed = json_decode($this->client->getResponse()->getContent());

        return (new JSONPath($responseFeed))->find($jsonPath);
    }

    /**
     * @Then /^there should be an ID (in this feed JSON paths "([^"]*)")$/
     */
    public function theClerkCrawlerShouldReceiveTheFollowingFeed(JSONPath $jsonPaths): void
    {
        Assert::minCount($jsonPaths, 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::integer($jsonPath);
        }
    }

    /**
     * @Then /^there should be the value "([^"]+)" (in this feed JSON paths "([^"]*)")$/
     */
    public function thisFeedShouldHaveValueInTheJsonPaths(string $value, JSONPath $jsonPaths): void
    {
        Assert::greaterThanEq($jsonPaths->count(), 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::eq($jsonPath, $value);
        }
    }

    /**
     * @Then /^there should be the value "([^"]+)" (in this feed JSON path "([^"]*)")$/
     */
    public function thisFeedShouldHaveValueInTheJsonPath(string $value, JSONPath $jsonPath): void
    {
        Assert::eq($jsonPath->first(), $value);
    }

    /**
     * @Then /^there shouldn\'t be any value (in this feed JSON paths "([^"]*)")$/
     */
    public function thereShouldntBeAnyValueInThisFeedJsonPath(JSONPath $jsonPaths): void
    {
        Assert::count($jsonPaths, 0);
    }

    /**
     * @Then /^there should be a value matching the pattern "([^"]*)" (in this feed JSON paths "([^"]*)")$/
     */
    public function thereShouldAValueMatchingThePatternInThisFeedJsonPaths(string $pattern, JSONPath $jsonPaths): void
    {
        Assert::greaterThanEq($jsonPaths->count(), 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::regex($jsonPath, $pattern);
        }
    }

    /**
     * @Then /^there should be a value matching the pattern "([^"]*)" (in this feed JSON path "([^"]*)")$/
     */
    public function thereShouldAValueMatchingThePatternInThisFeedJsonPath(string $pattern, JSONPath $jsonPath): void
    {
        Assert::regex($jsonPath->first(), $pattern);
    }

    /**
     * @Then /^there should be an empty array (in this feed JSON paths "([^"]*)")$/
     */
    public function thereShouldBeAnEmptyArrayInThisFeedJsonPaths(JSONPath $jsonPaths): void
    {
        Assert::greaterThanEq($jsonPaths->count(), 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::isArray($jsonPath->data());
            Assert::isEmpty($jsonPath->data());
        }
    }

    /**
     * @Then /^there should be an array with exactly one ID (in this feed JSON paths "([^"]*)")$/
     */
    public function thereShouldBeAnArrayWithExactlyOneIdInThisFeedJsonPaths(JSONPath $jsonPaths): void
    {
        Assert::greaterThanEq($jsonPaths->count(), 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::isArray($jsonPath->data());
            $array = $jsonPath->data();
            Assert::count($array, 1);
            Assert::allInteger($array);
            Assert::allGreaterThanEq($array, 1);
        }
    }

    /**
     * @Then /^there should be a Unix timestamp (in this feed JSON path "([^"]*)")$/
     */
    public function thereShouldBeAUnixTimestampInThisFeedJsonPath(JSONPath $jsonPath): void
    {
        // See: https://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
        Assert::integer($jsonPath->first());
        Assert::greaterThanEq($jsonPath->first(), ~\PHP_INT_MAX);
        Assert::lessThanEq($jsonPath->first(), \PHP_INT_MAX);
    }

    /**
     * @Then /^there should be the boolean value "([^"]*)" (in this feed JSON path "([^"]*)")$/
     */
    public function thereShouldBeTheBooleanValueInThisFeedJsonPath(string $booleanValueString, JSONPath $jsonPath): void
    {
        $booleanValue = filter_var($booleanValueString, \FILTER_VALIDATE_BOOLEAN);
        Assert::boolean(
            $booleanValue,
            'Expected a boolean string (like "true" or "false"), got: ' . $booleanValueString,
        );
        Assert::eq($jsonPath->first(), $booleanValue);
    }

    /**
     * @Then /^there should be a count of (\d+) elements? (in this feed JSON path "([^"]*)")$/
     */
    public function thereShouldBeACountOfElementInThisFeedJsonPath(int $count, JSONPath $jsonPath): void
    {
        Assert::count($jsonPath->first(), $count);
    }

    /**
     * @Then /^there should be an empty array (in this feed JSON path "([^"]*)")$/
     */
    public function thereShouldBeAnEmptyArrayInThisFeedJsonPath(JSONPath $jsonPath): void
    {
        Assert::isArray($jsonPath->first()->data());
        Assert::isEmpty($jsonPath->first()->data());
    }

    /**
     * @Given /^there should be an email (in this feed JSON paths "([^"]*)")$/
     */
    public function thereShouldBeAnEmailInThisFeedJSONPaths(JSONPath $jsonPaths): void
    {
        Assert::minCount($jsonPaths, 1);
        foreach ($jsonPaths as $jsonPath) {
            Assert::true(filter_var($jsonPath, \FILTER_VALIDATE_EMAIL) !== false);
        }
    }
}
