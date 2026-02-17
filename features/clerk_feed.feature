@clerk_plugin
Feature: Providing a Clerk.io data feed
    In order to integrate my store with Clerk.io
    As a Store Owner
    I would like to provide a Clerk.io data feed with products, taxons and sales like documented on https://docs.clerk.io/docs/data-feed

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Mugs" taxonomy
        And the store has a product "Sylius Mug" priced at "$3.99"
        And I assigned this product to "Mugs" taxon
        And the store has a product "Symfony Mug" priced at "$2.99"
        And I assigned this product to "Mugs" taxon
        And this product has an image "symfony_logo.png" with "main" type
        And this product description is "Great Symfony mug for the real developer"
        And this product original price is "$3.50" in "United States" channel
        And this product has a text attribute "Text Attribute" with value "Text Value"
        And there is a customer "customer@sylius.com" that placed an order
        And the customer bought a single "Sylius Mug"
        And the store has customer "Plughin Webgriffe" with email "iamplughin@webgriffe.com"

    Scenario: Providing Clerk data feed with products data
        When the Clerk crawler hits the data feed URL for the "United States" channel in the "English (United States)" locale and for the "products" resource
        Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
        And there should be an ID in this feed JSON paths "$.*.id"
        And there should be the value "SYLIUS_MUG" in this feed JSON path "$.[0].product_code"
        And there should be the value "SYMFONY_MUG" in this feed JSON path "$.[1].product_code"
        And there should be the value "Sylius Mug" in this feed JSON path "$.[0].name"
        And there should be the value "Symfony Mug" in this feed JSON path "$.[1].name"
        And there shouldn't be any value in this feed JSON paths "$.[0].description"
        And there should be the value "Great Symfony mug for the real developer" in this feed JSON paths "$.[1].description"
        And there should be the value "3.99" in this feed JSON paths "$.[0].price"
        And there should be the value "3.99" in this feed JSON paths "$.[0].list_price"
        And there should be the value "2.99" in this feed JSON paths "$.[1].price"
        And there should be the value "3.50" in this feed JSON paths "$.[1].list_price"
        And there shouldn't be any value in this feed JSON paths "$.[0].image"
        And there should be a value matching the pattern "|http://us.store.com/media/cache/resolve/sylius_medium/.+|" in this feed JSON paths "$.[1].image"
        And there should be the value "http://us.store.com/en_US/products/sylius-mug" in this feed JSON path "$.[0].url"
        And there should be the value "http://us.store.com/en_US/products/symfony-mug" in this feed JSON path "$.[1].url"
        And there should be an array with exactly one ID in this feed JSON paths "$.*.categories"
        And there should be the value "Text Value" in this feed JSON path "$.[1].attribute_Text_Attribute"

    Scenario: Providing Clerk data feed with taxons data
        When the Clerk crawler hits the data feed URL for the "United States" channel in the "English (United States)" locale and for the "categories" resource
        Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
        And there should be a count of 1 element in this feed JSON path "$."
        And there should be an ID in this feed JSON paths "$.*.id"
        And there should be the value "Mugs" in this feed JSON path "$.[0].name"
        And there should be the value "http://us.store.com/en_US/taxons/mugs" in this feed JSON path "$.[0].url"
        And there should be an empty array in this feed JSON path "$.[0].subcategories"

    Scenario: Providing Clerk data feed with sales data
        When the Clerk crawler hits the data feed URL for the "United States" channel in the "English (United States)" locale and for the "orders" resource
        Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
        And there should be an ID in this feed JSON paths "$.*.id"
        And there should be an ID in this feed JSON paths "$.*.customer"
        And there should be an email in this feed JSON paths "$.*.email"
        And there should be a Unix timestamp in this feed JSON path "$.*.time"
        And there should be an ID in this feed JSON paths "$.*.products.*.id"
        And there should be the value "1" in this feed JSON paths "$.*.products.*.quantity"
        And there should be the value "3.99" in this feed JSON paths "$.*.products.*.price"

    Scenario: Providing Clerk data feed with customers data
        When the Clerk crawler hits the data feed URL for the "United States" channel in the "English (United States)" locale and for the "customers" resource
        Then the Clerk crawler should receive a successful HTTP response with a valid JSON feed as its content
        And there should be an ID in this feed JSON paths "$.*.id"
        And there should be an email in this feed JSON paths "$.*.email"
        And there should be the value "Plughin Webgriffe" in this feed JSON paths "$.[0].name"
