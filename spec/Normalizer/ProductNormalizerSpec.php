<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Normalizer;

use Liip\ImagineBundle\Service\FilterService;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Normalizer\ProductNormalizer;

class ProductNormalizerSpec extends ObjectBehavior
{
    /**
     * @var ProductInterface
     */
    private $product;
    /**
     * @var ChannelInterface
     */
    private $channel;
    /**
     * @var ProductVariantInterface
     */
    private $productVariant;
    /**
     * @var ChannelPricingInterface
     */
    private $channelPricing;

    function let(
        ProductVariantResolverInterface $productVariantResolver,
        RouterInterface $router,
        FilterService $imagineFilterService
    ) {
        $this->product = new Product();
        $this->product->setCurrentLocale('en_US');
        $this->channel = new Channel();
        $this->channel->setCode('DEFAULT');
        $locale = new Locale();
        $locale->setCode('en_US');
        $this->channel->setDefaultLocale($locale);
        $this->productVariant = new ProductVariant();
        $this->productVariant->setProduct($this->product);
        $this->product->addVariant($this->productVariant);
        $this->channelPricing = new ChannelPricing();
        $this->channelPricing->setPrice(399);
        $this->channelPricing->setChannelCode('DEFAULT');
        $this->channelPricing->setProductVariant($this->productVariant);
        $this->productVariant->addChannelPricing($this->channelPricing);
        $this->beConstructedWith($productVariantResolver, $router, $imagineFilterService);
    }

    function it_is_initializable_and_implements_normalizer_interface(): void
    {
        $this->shouldHaveType(ProductNormalizer::class);
        $this->shouldHaveType(NormalizerInterface::class);
    }

    function it_supports_product_normalization_for_clerk_array_format()
    {
        $this->supportsNormalization(new Product(), 'clerk_array')->shouldBe(true);
    }

    function it_does_not_support_product_normalization_for_other_formats()
    {
        $this->supportsNormalization(new Product(), 'json')->shouldReturn(false);
        $this->supportsNormalization(new Product(), 'other_format')->shouldReturn(false);
    }

    function it_does_not_support_other_object_normalization()
    {
        $this->supportsNormalization(new \stdClass(), 'clerk_array')->shouldReturn(false);
    }

    function it_throws_an_exception_if_channel_is_not_in_context()
    {
        $product = new Product();
        $this->shouldThrow(InvalidArgumentException::class)->during('normalize', [$product, null, []]);
    }

    function it_throws_an_exception_if_channel_in_context_is_not_valid()
    {
        $product = new Product();
        $this->shouldThrow(\InvalidArgumentException::class)->during(
            'normalize',
            [$product, null, ['channel' => new \stdClass()]]
        );
    }

    function it_throws_an_exception_if_invalid_object_is_given()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during(
            'normalize',
            [new \stdClass(), null, ['channel' => new Channel()]]
        );
    }

    function it_normalize_null_price_if_product_has_no_variant(ProductVariantResolverInterface $productVariantResolver)
    {
        $productVariantResolver->getVariant($this->product)->willReturn(null);

        $this->normalize($this->product, null, ['channel' => $this->channel])['price']->shouldBeNull();
    }

    function it_normalize_null_price_if_product_has_variant_without_price_in_channel(
        ProductVariantResolverInterface $productVariantResolver
    ) {
        $this->productVariant->removeChannelPricing($this->channelPricing);
        $productVariantResolver->getVariant($this->product)->willReturn($this->productVariant);

        $this->normalize($this->product, null, ['channel' => $this->channel])['price']->shouldBeNull();
    }

    function it_normalize_null_price_if_product_has_variant_with_null_price_in_channel(
        ProductVariantResolverInterface $productVariantResolver
    ) {
        $this->channelPricing->setPrice(null);
        $productVariantResolver->getVariant($this->product)->willReturn($this->productVariant);

        $this->normalize($this->product, null, ['channel' => $this->channel])['price']->shouldBeNull();
    }
}
