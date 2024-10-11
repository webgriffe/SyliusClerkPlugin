# Installation

1. Run `composer require webgriffe/sylius-clerk-plugin`.

2. Add the plugin to the `config/bundles.php` file if not already done automatically:

   ```php
   Webgriffe\SyliusClerkPlugin\WebgriffeSyliusClerkPlugin::class => ['all' => true],
   ```

3. Add the plugin's configuration by creating the file `config/packages/webgriffe_sylius_clerk_plugin.yaml.yaml` with the following content:

   ```yaml
    imports:
        - { resource: "@WebgriffeSyliusClerkPlugin/config/config.yaml" }
   ```

4. Add the plugin's routing by creating the file `config/routes.yaml` with the following content:

   ```yaml
    webgriffe_sylius_clerk_shop:
        resource: "@WebgriffeSyliusClerkPlugin/config/shop_routing.php"
        prefix: /{_locale}
        requirements:
            _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$

    webgriffe_sylius_clerk_feed:
        resource: "@WebgriffeSyliusClerkPlugin/config/feed_routing.php"
   ```

5. Finish the installation by installing assets:

   ```bash
   bin/console assets:install
   bin/console sylius:theme:assets:install
   ```
