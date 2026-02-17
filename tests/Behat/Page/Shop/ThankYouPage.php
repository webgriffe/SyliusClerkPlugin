<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Page\Shop;

use Sylius\Behat\Page\Shop\Order\ThankYouPage as BaseThankYouPage;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

class ThankYouPage extends BaseThankYouPage implements ThankYouPageInterface
{
    public function assertClerkSalesTrackingForOrder(OrderInterface $order): void
    {
        $clerkSalesTracking = $this->getElement('clerk_sales_tracking');
        Assert::eq($order->getId(), $clerkSalesTracking->getAttribute('data-sale'));
        Assert::eq($order->getCustomer()->getEmail(), $clerkSalesTracking->getAttribute('data-email'));
        Assert::eq($order->getCustomer()->getId(), $clerkSalesTracking->getAttribute('data-customer'));
        $products = json_decode($clerkSalesTracking->getAttribute('data-products'), true);
        Assert::isArray($products);
        Assert::count($products, $order->getItems()->count());
        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->toArray()[0];
        Assert::eq($products[0]['id'], $orderItem->getVariant()->getProduct()->getId());
        Assert::eq($products[0]['quantity'], $orderItem->getQuantity());
        Assert::eq($products[0]['price'], $orderItem->getUnitPrice() / 100);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'clerk_sales_tracking' => 'span.clerk[data-api="log/sale"]',
        ]);
    }
}
