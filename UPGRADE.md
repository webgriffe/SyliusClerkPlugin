## From 2.x to 3.x

The v3 of the plugin now uses the new feed v2 generation. We suggest to upgrade to this version as soon as possible by
reading the docs of the plugin. The feed v1 is deprecated and will be removed in the next major version of the plugin. 

- Replace `Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider` with `webgriffe_sylius_clerk.provider.private_api_key`.
- Replace `Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider` with `webgriffe_sylius_clerk.provider.public_api_key`.
- Replace `@WebgriffeSyliusClerkPlugin/Resources/config/shop_routing.yml` with `@WebgriffeSyliusClerkPlugin/config/shop_routing.php`.
- Remove `@WebgriffeSyliusClerkPlugin/Resources/config/admin_routing.yml`.
- Replace `@WebgriffeSyliusClerkPlugin/Resources/config/feed_routing.yml` with `@WebgriffeSyliusClerkPlugin/config/feed_routing.php`.
- Remove any parameter `webgriffe_sylius_clerk.storage_feed_path` usage.
