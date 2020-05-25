<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

class ClerkTrackingCodeContext implements Context
{
    /** @var HomePageInterface */
    private $homePage;

    public function __construct(HomePageInterface $homePage)
    {
        $this->homePage = $homePage;
    }

    /**
     * @When /^I open the homepage$/
     */
    public function iOpenTheHomepage()
    {
        $this->homePage->open();
    }

    /**
     * @Then /^there should be the Clerk\.js tracking code in the response body with the public API key "([^"]*)"$/
     */
    public function thereShouldBeTheClerkJsTrackingCodeInTheResponseBodyWithThePublicAPIKey(string $publicApiKey): void
    {
        Assert::contains($this->homePage->getContent(), 'clerk.js');
        Assert::contains($this->homePage->getContent(), $publicApiKey);
    }
}
