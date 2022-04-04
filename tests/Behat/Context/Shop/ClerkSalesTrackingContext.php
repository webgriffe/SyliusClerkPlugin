<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Shop;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\Webgriffe\SyliusClerkPlugin\Behat\Page\Shop\ThankYouPageInterface;

final class ClerkSalesTrackingContext implements Context
{
    public function __construct(private ThankYouPageInterface $thankYouPage)
    {
    }

    /**
     * @Then /^there should be the Clerk sales tracking code for the (latest order) just placed$/
     */
    public function thereShouldBeTheClerkSalesTrackingCodeForTheLatestOrderJustPlaced(OrderInterface $order): void
    {
        $this->thankYouPage->assertClerkSalesTrackingForOrder($order);
    }
}
