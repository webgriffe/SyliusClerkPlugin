@clerk_plugin
Feature: Providing a Clerk.io data feed
  In order to integrate my store with Clerk.io
  As a Store Owner
  I would like to provide a Clerk.io data feed with products, categories and sales like documented on https://docs.clerk.io/docs/data-feed

  Scenario: Providing a simple Clerk data feed with products
    Given the store operates on a single channel in "United States"
    And the store has a product "Sylius Mug"
    And this product has an image "sylius_logo.png" with "main" type
    And the store has a product "Symfony Mug"
    And this product has an image "symfony_logo.png" with "main" type
    When the Clerk crawler hits the data feed URL for the "United States" channel
    Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
    And there should be an ID in this feed JSON paths "$.products.*.id"
    And there should be the value "SYLIUS_MUG" in this feed JSON path "$.products[0].sku"
    And there should be the value "SYMFONY_MUG" in this feed JSON path "$.products[1].sku"
    And there should be the value "Sylius Mug" in this feed JSON path "$.products[0].name"
    And there should be the value "Sylius Mug" in this feed JSON path "$.products[0].name"
    And there should be the value "Symfony Mug" in this feed JSON path "$.products[1].name"
    And there shouldn't be any value in this feed JSON paths "$.products.*.description"
    And there should be the value "1" in this feed JSON paths "$.products.*.price"
    And there should be a value matching the pattern "|http://localhost/media/cache/sylius_shop_product_thumbnail/.+|" in this feed JSON paths "$.products.*.image"
    And there should be the value "http://localhost/en_US/products/sylius-mug" in this feed JSON path "$.products[0].url"
    And there should be the value "http://localhost/en_US/products/symfony-mug" in this feed JSON path "$.products[1].url"
    And there should be an empty array in this feed JSON paths "$.products.*.categories"
#      """
#      {
#          "products": [
#              {
#                  "id": <id>,
#                  "name": "Sylius Mug",
#                  "description": "",
#                  "price": 1,
#                  "image": "http://localhost:8080/media/cache/resolve/sylius_shop_product_original/<product_image_path>",
#                  "url": "http://localhost:8080/en_US/products/sylius-mug",
#                  "categories": [],
#                  "sku": "SYLIUS_MUG"
#              },
#              {
#                  "id": <id>,
#                  "name": "Symfony Mug",
#                  "description": "",
#                  "price": 1,
#                  "image": "http://localhost:8080/media/cache/resolve/sylius_shop_product_original/<product_image_path>",
#                  "url": "http://localhost:8080/en_US/products/symfony-mug",
#                  "categories": [],
#                  "sku": "SYMFONY_MUG"
#              }
#          ]
#      }
#      """
