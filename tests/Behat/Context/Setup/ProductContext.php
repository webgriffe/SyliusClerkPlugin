<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\DefaultProductVariantResolver;

class ProductContext implements Context
{
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var DefaultProductVariantResolver
     */
    private $defaultVariantResolver;

    public function __construct(ObjectManager $objectManager, DefaultProductVariantResolver $defaultVariantResolver)
    {
        $this->objectManager = $objectManager;
        $this->defaultVariantResolver = $defaultVariantResolver;
    }

    /**
     * @Given /^(this product) description is "([^"]*)"$/
     */
    public function thisProductDescriptionIs(ProductInterface $product, string $description)
    {
        $product->setDescription($description);
        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) original price is ("[^"]*") in ("([^"]*)" channel)$/
     */
    public function thisProductOriginalPriceIsInChannel(
        ProductInterface $product,
        int $originalPrice,
        ChannelInterface $channel
    ) {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        $channelPricing->setOriginalPrice($originalPrice);

        $this->objectManager->flush();
    }
}
