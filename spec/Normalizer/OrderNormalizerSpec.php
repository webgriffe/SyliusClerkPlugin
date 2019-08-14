<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Normalizer\OrderNormalizer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OrderNormalizerSpec extends ObjectBehavior
{
    private const CHECKOUT_COMPLETED_AT_TIMESTAMP = 1564998840;

    /**
     * @var OrderInterface
     */
    private $order;

    function let()
    {
        $this->order = new Order();
        $this->setPrivateProperty($this->order, 'id', 1);
        $customer = new Customer();
        $this->setPrivateProperty($customer, 'id', 1);
        $customer->setEmail('customer@sylius.com');
        $this->order->setCustomer($customer);
        $this->order->setCheckoutCompletedAt((new \DateTime())->setTimestamp(self::CHECKOUT_COMPLETED_AT_TIMESTAMP));
    }

    function it_is_initializable_and_implements_serializer_interface()
    {
        $this->shouldHaveType(OrderNormalizer::class);
        $this->shouldHaveType(NormalizerInterface::class);
    }

    function it_supports_order_normalization_for_clerk_json_format()
    {
        $this->supportsNormalization(new Order(), 'clerk_json')->shouldReturn(true);
    }

    function it_does_not_support_order_normalization_for_other_formats()
    {
        $this->supportsNormalization(new Order(), 'json')->shouldReturn(false);
        $this->supportsNormalization(new Order(), 'other_format')->shouldReturn(false);
    }

    function it_does_not_support_other_object_normalization()
    {
        $this->supportsNormalization(new \stdClass(), 'clerk_json')->shouldReturn(false);
    }

    function it_throws_an_exception_normalizing_object_which_is_not_an_order()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('normalize', [new \stdClass(), null, []]);
    }

    function it_normalize_an_order_without_products()
    {
        $this->normalize($this->order)->shouldBeLike(
            [
                'id' => 1,
                'products' => [],
                'time' => self::CHECKOUT_COMPLETED_AT_TIMESTAMP,
                'email' => 'customer@sylius.com',
                'customer' => 1
            ]
        );
    }

    function it_normalize_an_order_with_products()
    {
        $product1 = new Product();
        $this->setPrivateProperty($product1, 'id', 1);
        $productVariant1 = new ProductVariant();
        $productVariant1->setProduct($product1);
        $product1->addVariant($productVariant1);
        $product2 = new Product();
        $this->setPrivateProperty($product2, 'id', 2);
        $productVariant2 = new ProductVariant();
        $productVariant2->setProduct($product2);
        $product2->addVariant($productVariant2);
        $orderItem1 = new OrderItem();
        $this->setPrivateProperty($orderItem1, 'quantity', 1);
        $orderItem1->setVariant($productVariant1);
        $orderItem1->setUnitPrice(200 * 100);
        $this->order->addItem($orderItem1);
        $orderItem2 = new OrderItem();
        $this->setPrivateProperty($orderItem2, 'quantity', 2);
        $orderItem2->setVariant($productVariant2);
        $orderItem2->setUnitPrice((int)(120.99 * 100));
        $this->order->addItem($orderItem2);

        $this->normalize($this->order)->shouldBeLike(
            [
                'id' => 1,
                'products' => [
                    [
                        'id' => 1,
                        'quantity' => 1,
                        'price' => 200.00
                    ],
                    [
                        'id' => 2,
                        'quantity' => 2,
                        'price' => 120.99
                    ],
                ],
                'time' => self::CHECKOUT_COMPLETED_AT_TIMESTAMP,
                'email' => 'customer@sylius.com',
                'customer' => 1
            ]
        );
    }

    private function setPrivateProperty($instance, string $property, $value)
    {
        $reflection = new \ReflectionClass($instance);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($instance, $value);
    }
}
