@clerk_plugin
Feature: Providing the Clerk sales tracking
  In order to track sales with Clerk.io
  As a Store Owner
  I would like to include the Clerk sales tracking code in the order thank you page

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "PHP T-Shirt" priced at "$19.99"
    And the store ships everywhere for free
    And the store has a payment method "Offline" with a code "OFFLINE"
    And I am a logged in customer
    And I have product "PHP T-Shirt" in the cart

  Scenario: Providing the Clerk sales tracking
    When I proceed selecting "Offline" payment method
    And I confirm my order
    Then I should see the thank you page
    Then there should be the Clerk sales tracking code for the latest order just placed
