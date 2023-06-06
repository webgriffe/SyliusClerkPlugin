<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Normalizer\CustomerNormalizer;
use PhpSpec\ObjectBehavior;

class CustomerNormalizerSpec extends ObjectBehavior
{
    private CustomerInterface $customer;

    function let()
    {
        $this->customer = new Customer();
        $this->setPrivateProperty($this->customer, 'id', 1);
        $this->customer->setEmail('iamplughin@webgriffe.com');
        $this->customer->setFirstName('Plughin');
        $this->customer->setLastName('Webgriffe');
        $this->customer->setGender('neutral');
    }

    function it_is_initializable_and_implements_serializer_interface()
    {
        $this->shouldHaveType(CustomerNormalizer::class);
        $this->shouldHaveType(NormalizerInterface::class);
    }

    function it_supports_customer_normalization_for_clerk_array_format()
    {
        $this->supportsNormalization(new Customer(), 'clerk_array')->shouldReturn(true);
    }

    function it_does_not_support_customer_normalization_for_other_formats()
    {
        $this->supportsNormalization(new Customer(), 'json')->shouldReturn(false);
        $this->supportsNormalization(new Customer(), 'other_format')->shouldReturn(false);
    }

    function it_does_not_support_other_object_normalization()
    {
        $this->supportsNormalization(new \stdClass(), 'clerk_array')->shouldReturn(false);
    }

    function it_throws_an_exception_normalizing_object_which_is_not_an_order()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('normalize', [new \stdClass(), null, []]);
    }

    function it_normalize_an_order_without_products()
    {
        $this->normalize($this->customer)->shouldBeLike(
            [
                'id'     => 1,
                'name'   => 'Plughin Webgriffe',
                'email'  => 'iamplughin@webgriffe.com',
                'gender' => 'neutral',
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
