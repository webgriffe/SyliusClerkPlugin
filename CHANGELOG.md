## From 2.x to 3.x

### Breaking Changes

- Service `Webgriffe\SyliusClerkPlugin\Service\PrivateApiKeyProvider` has been removed. Use `webgriffe_sylius_clerk.provider.private_api_key` instead.
- Service `Webgriffe\SyliusClerkPlugin\Service\PublicApiKeyProvider` has been removed. Use `webgriffe_sylius_clerk.provider.public_api_key` instead.
- The file `@WebgriffeSyliusClerkPlugin/Resources/config/shop_routing.yml` has been removed. Use `@WebgriffeSyliusClerkPlugin/config/shop_routing.php` instead.
- The file `@WebgriffeSyliusClerkPlugin/Resources/config/admin_routing.yml` has been removed.
- The file `@WebgriffeSyliusClerkPlugin/Resources/config/feed_routing.yml` has been removed. Use `@WebgriffeSyliusClerkPlugin/config/feed_routing.php` instead.
- The parameter `webgriffe_sylius_clerk.storage_feed_path` has been removed.
