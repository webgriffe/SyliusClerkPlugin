@clerk_plugin
Feature: Providing a Clerk.io data feed
  In order to integrate my store with Clerk.io
  As a Store Owner
  I would like to provide a Clerk.io data feed with products, categories and sales like documented on https://docs.clerk.io/docs/data-feed

  Scenario: Providing a simple Clerk data feed with products
    Given the store operates on a single channel in "United States"
    And the store has "Mugs" taxonomy
    And the store has a product "Sylius Mug"
    And I assigned this product to "Mugs" taxon
    And the store has a product "Symfony Mug"
    And I assigned this product to "Mugs" taxon
    And this product has an image "symfony_logo.png" with "main" type
    And this product description is "Great Symfony mug for the real developer"
    When the Clerk crawler hits the data feed URL for the "United States" channel
    Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
    And there should be an ID in this feed JSON paths "$.products.*.id"
    And there should be the value "SYLIUS_MUG" in this feed JSON path "$.products[0].sku"
    And there should be the value "SYMFONY_MUG" in this feed JSON path "$.products[1].sku"
    And there should be the value "Sylius Mug" in this feed JSON path "$.products[0].name"
    And there should be the value "Sylius Mug" in this feed JSON path "$.products[0].name"
    And there should be the value "Symfony Mug" in this feed JSON path "$.products[1].name"
    And there shouldn't be any value in this feed JSON paths "$.products[0].description"
    And there should be the value "Great Symfony mug for the real developer" in this feed JSON paths "$.products[1].description"
    And there should be the value "1" in this feed JSON paths "$.products.*.price"
    And there shouldn't be any value in this feed JSON paths "$.products[0].image"
    And there should be a value matching the pattern "|http://localhost/media/cache/sylius_shop_product_thumbnail/.+|" in this feed JSON paths "$.products[1].image"
    And there should be the value "http://localhost/en_US/products/sylius-mug" in this feed JSON path "$.products[0].url"
    And there should be the value "http://localhost/en_US/products/symfony-mug" in this feed JSON path "$.products[1].url"
    And there should be an array with exactly one ID in this feed JSON paths "$.products.*.categories"
