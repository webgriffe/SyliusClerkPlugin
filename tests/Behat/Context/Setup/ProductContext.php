<?php

declare(strict_types=1);

namespace Tests\Webgriffe\SyliusClerkPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

class ProductContext implements Context
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    public function __construct(ProductRepositoryInterface $productRepository, SharedStorageInterface $sharedStorage)
    {
        $this->productRepository = $productRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^(this product) description is "([^"]*)"$/
     */
    public function thisProductDescriptionIs(ProductInterface $product, string $description)
    {
        $product->setDescription($description);
        $this->saveProduct($product);
    }

    private function saveProduct(ProductInterface $product)
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product', $product);
    }
}
