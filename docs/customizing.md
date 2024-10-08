# Customizing

Basically, this plugin provides an easy way to generate a JSON feed compliant with
the [Clerk.io data feed specifications](https://help.clerk.io/platform-guides/custom/data-sync/json-data-feed-v2/).
Each resource type is handled by a specific provider and a specific normalizer. The resources handled by the plugin are:

* Products
* Categories (a.k.a. Taxons on Sylius)
* Orders
* Customers
* Pages

For each entity type the following two components are involved in feed generation:

* A ResourceFeedGenerator. It invokes the resource provider and pass the result to the normalizer. It returns a feed object.
* A QueryBuilderResourceProvider. It implements by default the ResourceProviderInterface and it is used by products, orders, customers and categories.
  It calls a specific query builder. Each query builder dispatch a QueryBuilderEvent to allow customization of the query.
* The PagesProvider is specific for pages, and it is used to get the pages to be indexed. It gets data from the plugin configuration.
* A Normalizer. It implements the NormalizerInterface and it is used to normalize the data from the resource provider to the Clerk.io format.

So, to customize the feed generation you can replace these implementations using the common Symfony techniques to do
so (see [here](https://symfony.com/doc/current/bundles/override.html#services-configuration)).
