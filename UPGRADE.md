## From 3.x to 4.x

In this version, we have updated the plugin to be compatible with version 2 of Sylius.

- The route `@WebgriffeSyliusClerkPlugin/config/shop_routing.php` has been renamed to `@WebgriffeSyliusClerkPlugin/config/routes/shop.php`.
- The route `@WebgriffeSyliusClerkPlugin/config/feed_routing.php` has been renamed to `@WebgriffeSyliusClerkPlugin/config/routes/feed.php`.
- The file `@WebgriffeSyliusClerkPlugin/config/config.yaml` has been renamed to `@WebgriffeSyliusClerkPlugin/config/config.php`.
- Various deprecated classes and their services have been removed. Please migrate to use the new feed v2 generation and the new API key providers as described in the documentation of the plugin.
- The private_api_key configuration key under stores has been removed.

## From 2.x to 3.x

The v3 of the plugin now uses the new feed v2 generation. We suggest to upgrade to this version as soon as possible by
reading the docs of the plugin. The feed v1 is deprecated and will be removed in the next major version of the plugin. 

- Replace `Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider` with `webgriffe_sylius_clerk.provider.private_api_key`.
- Replace `Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider` with `webgriffe_sylius_clerk.provider.public_api_key`.
- Replace `@WebgriffeSyliusClerkPlugin/Resources/config/shop_routing.yml` with `@WebgriffeSyliusClerkPlugin/config/shop_routing.php`.
- Remove `@WebgriffeSyliusClerkPlugin/Resources/config/admin_routing.yml`.
- Replace `@WebgriffeSyliusClerkPlugin/Resources/config/feed_routing.yml` with `@WebgriffeSyliusClerkPlugin/config/feed_routing.php`.
- Remove any parameter `webgriffe_sylius_clerk.storage_feed_path` usage.
