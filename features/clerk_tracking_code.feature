@clerk_plugin
Feature: Providing the Clerk.js tracking code
  In order to integrate my store with Clerk.io
  As a Store Owner
  I would like to include the Clerk.js tracking code in all pages

  Background:
    Given the store operates on a single channel in "United States"

  Scenario: Providing the Clerk.js tracking code
    When I open the homepage
    Then there should be the Clerk.js tracking code in the response body with the public API key "public-key"
