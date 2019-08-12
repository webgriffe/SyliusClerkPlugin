<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface as BaseThankYouPageInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ThankYouPageInterface extends BaseThankYouPageInterface
{
    public function assertClerkSalesTrackingForOrder(OrderInterface $order): void;
}
