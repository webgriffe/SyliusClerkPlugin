@clerk_plugin
Feature: Providing a Clerk.io data feed
  In order to integrate my store with Clerk.io
  As a Store Owner
  I would like to provide a Clerk.io data feed with products, categories and sales like documented on https://docs.clerk.io/docs/data-feed

  Scenario: Providing a simple Clerk data feed with products
    Given the store operates on a single channel in "United States"
    And the store has "Sylius Mug" and "Symfony Mug" products
    When the Clerk crawler hits the data feed URL for the "United States" channel
    Then a Clerk feed with products "Sylius Mug" and "Symfony Mug" should be received
