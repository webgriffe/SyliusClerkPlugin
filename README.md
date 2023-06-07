<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>
<h1 align="center">Clerk.io Plugin</h1>
<p align="center">This plugin integrates your Sylius store with <a href="https://clerk.io/">Clerk.io</a>, the world's #1 e‑commerce personalization platform (AI) for product recommendations.

</p>
<p align="center"><a href="https://github.com/webgriffe/SyliusClerkPlugin/actions"><img src="https://github.com/webgriffe/SyliusClerkPlugin/workflows/Build/badge.svg" alt="Build Status" /></a></p>



## Installation

1. Run `composer require webgriffe/sylius-clerk-plugin`.

2. Add the plugin to the `config/bundles.php` file:

   ```php
   Webgriffe\SyliusClerkPlugin\WebgriffeSyliusClerkPlugin::class => ['all' => true],
   ```

3. Add the plugin's routing by creating the file `config/routes.yaml` with the following content:

   ```yaml
   webgriffe_sylius_clerk_shop:
       resource: "@WebgriffeSyliusClerkPlugin/Resources/config/shop_routing.yml"
       prefix: /{_locale}
       requirements:
           _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
   
   webgriffe_sylius_clerk_admin:
       resource: "@WebgriffeSyliusClerkPlugin/Resources/config/admin_routing.yml"
       prefix: /admin
   
   webgriffe_sylius_clerk_feed:
       resource: "@WebgriffeSyliusClerkPlugin/Resources/config/feed_routing.yml"
   
   ```

5. Finish the installation by installing assets:

   ```bash
   bin/console assets:install
   bin/console sylius:theme:assets:install
   ```
   

## Configuration

The Clerk.io integration with Sylius is per-channel. Every Clerk.io store must be syncronized with just only one Sylius channel. So, to configure this plugin you must create a file in `config/packages/webgriffe_sylius_clerk.yaml` with the following contents:

```yaml
webgriffe_sylius_clerk:
  # optional - if you want to change the default feed path
  # storage_feed_path: '%kernel.project_dir%/%env(CLERK_FEED_PATH)%'  
  # storage_feed_path: '%kernel.project_dir%/var/storage'  
  stores:
    - channel_code: WEB-US
      public_api_key: web-us-public-key
      private_api_key: 123abc
    - channel_code: WEB-EU
      public_api_key: web-ew-public-key
      private_api_key: 890xyz
```

Where every entry in the `stores` key must contain the Sylius channel code in `channel_code` and the related Clerk's public/private API key in `public_api_key` and  `private_api_key`.

## Sync your data with Clerk.io

Login into your Clerk.io [dashboard](https://my.clerk.io/) and go to the **Data** page. In the **Data Sync Settings** section, select **Clerk.io JSON Feed** as **Sync Method** and enter the following JSON Feed URL:

```
https://your-sylius-store.com/clerk/feed/channelId
```

Where `https://your-sylius-store.com` is your Sylius store base URL and `channelId` is the database ID of the Sylius channel you whant to sync.

Or set up a cronjob for this command:

```bash
bin/console webgriffe:clerk:generate-feed channelCode
```

and use the following JSON Feed URL:

```
https://your-sylius-store.com/media/<channelCode>_clerk_feed.json
```

## Install Clerk.js on you store front

Like stated in the official Clerk documentation [here](https://docs.clerk.io/docs/clerkjs-quick-start#section-installing-clerkjs), you have to put the Clerk.js tracking code on all pages of your store just before the `</head>` tag. To do so this plugin expose a dedicated controller action which you can render in your Twig template, for example like the following:

```twig
{# templates/bundles/SyliusShopBundle/layout.html.twig #}

{% extends '@!SyliusShop/layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}

    {{ render(url('webgriffe_sylius_clerk_tracking_code')) }}
{% endblock %}
```

From then you can use all the Clerk.js features on your store pages.

## Install sales tracking on order success page

Like stated in the official Clerk documentation [here](https://help.clerk.io/en/articles/1130393-installing-sales-tracking-on-other-custom-platforms), you should optimise Clerk.io by installing the instant sales tracking code on your thank you page. To do so this plugin expose a dedicated controller action which you can render in your thank you page Twig template, for example like the following:

```twig
{# templates/bundles/SyliusShopBundle/Order/thankYou.html.twig #}
{% extends '@!SyliusShop/Order/thankYou.html.twig' %}

{% block javascripts %}
    {{ parent() }}

    {{ render(url('webgriffe_sylius_clerk_sales_tracking', {orderId: order.id})) }}
{% endblock %}
```

## Customizing

Basically, this bundle provides an easy way to generate a JSON feed compliant with the [Clerk.io data feed specifications](https://docs.clerk.io/docs/data-feed). The feed basically contains three arrays of the following entities:

* Products
* Categories (a.k.a. Taxons on Sylius)
* Orders
* Customers

For each entity type the following two components are involved in feed generation:

* A `Webgriffe\SyliusClerkPlugin\QueryBuilder\QueryBuilderFactoryInterface` which is responsible to create a `Doctrine\ORM\QueryBuilder` which builds the query to select the objects you want to include in the feed.
* A `Symfony\Component\Serializer\Normalizer\NormalizerInterface` which is a common normalizer of the [Symfony's Serializer component](https://symfony.com/doc/current/components/serializer.html). The normalizer is the component responsible to convert every instance of the related entity type to an associative array which is then converted to JSON. To the normalization process will be passed a special `clerk_array` format. So, your normalizers should support only normalization for the `clerk_array` format. In this way there's no risk to break other serializer usages for that objects. 

The plugin already provides three query builder factories and three normalizers:

- Products: `Webgriffe\SyliusClerkPlugin\QueryBuilder\ProductsQueryBuilderFactory` and `Webgriffe\SyliusClerkPlugin\Normalizer\ProductNormalizer`
- Categories: `Webgriffe\SyliusClerkPlugin\QueryBuilder\TaxonsQueryBuilderFactory` and `Webgriffe\SyliusClerkPlugin\Normalizer\TaxonNormalizer`
- Orders: `Webgriffe\SyliusClerkPlugin\QueryBuilder\OrdersQueryBuilderFactory` and `Webgriffe\SyliusClerkPlugin\Normalizer\OrderNormalizer`
- Customers: `Webgriffe\SyliusClerkPlugin\QueryBuilder\CustomersQueryBuilderFactory` and `Webgriffe\SyliusClerkPlugin\Normalizer\CustomerNormalizer`

So, to customize the feed generation you can replace these implementations using the common Symfony techniques to do so (see [here](https://symfony.com/doc/current/bundles/override.html#services-configuration)).

## Contributing

To contribute you need to:

1. Clone this repository into your development environment

2. [OPTIONAL] Copy the `.env` file inside the test application directory to the `.env.local` file:

   ```bash
   cp tests/Application/.env tests/Application/.env.local
   ```

   Then edit the `tests/Application/.env.local` file by setting configuration specific for you development environment.

3. Then, from the plugin's root directory, run the following commands:

   ```bash
   (cd tests/Application && yarn install)
   (cd tests/Application && yarn build)
   (cd tests/Application && APP_ENV=test bin/console assets:install public)
   (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
   (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
   ```
4. Run test application's webserver on `127.0.0.1:8080`:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```

4. Now at https://127.0.0.1:8080/ you have a full Sylius testing application which runs the plugin

### Testing

After your changes you must ensure that the tests are still passing. The current CI suite runs the following tests:

* Easy Coding Standard

  ```bash
  vendor/bin/ecs check src/ tests/Behat/
  ```

* PHPStan

  ```bash
  vendor/bin/phpstan analyse -c phpstan.neon -l max src/
  ```

* PHPUnit

  ```bash
  vendor/bin/phpunit
  ```

* PHPSpec

  ```bash
  vendor/bin/phpspec run
  ```

* Behat

  ```bash
  vendor/bin/behat --strict -vvv --no-interaction || vendor/bin/behat --strict -vvv --no-interaction --rerun
  ```

To run them all with a single command run:

```bash
composer suite
```

To run Behat's JS scenarios you need to setup Selenium and Chromedriver. Do the following:

1. [Install Symfony CLI command](https://symfony.com/download).

2. Start Headless Chrome:

      ```bash
      google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
      ```

4. Remember that the test application webserver must be up and running as described above:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```

License
-------
This library is under the MIT license. See the complete license in the LICENSE file.

Credits
-------
Developed by [Webgriffe®](http://www.webgriffe.com/).
