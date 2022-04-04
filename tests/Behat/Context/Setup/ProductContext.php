<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\DefaultProductVariantResolver;

final class ProductContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DefaultProductVariantResolver $defaultVariantResolver
    ) {
    }

    /**
     * @Given /^(this product) description is "([^"]*)"$/
     */
    public function thisProductDescriptionIs(ProductInterface $product, string $description): void
    {
        $product->setDescription($description);
        $this->entityManager->flush();
    }

    /**
     * @Given /^(this product) original price is ("[^"]*") in ("([^"]*)" channel)$/
     */
    public function thisProductOriginalPriceIsInChannel(
        ProductInterface $product,
        int $originalPrice,
        ChannelInterface $channel
    ): void {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        $channelPricing->setOriginalPrice($originalPrice);

        $this->entityManager->flush();
    }
}
