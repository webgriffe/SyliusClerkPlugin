# Configuration

The Clerk.io integration with Sylius is per-channel and locale. Every Clerk.io store must be synchronized with just only one Sylius channel/locale.
So, to configure this plugin you must create a file in `config/packages/webgriffe_sylius_clerk_plugin.yaml` with the following contents:

```yaml
webgriffe_sylius_clerk:
    webgriffe_sylius_clerk:
        storage_feed_path: '%kernel.project_dir%/public/feed/clerk.io' # optional - if you want to change the default feed path
        image_type: 'main' # default is 'main'
        image_filter_to_apply: 'sylius_medium' # default is 'sylius_medium'
        token_authentication_enabled: true # enable token authentication for Clerk.io feed
        stores:
            -   channel_code: WEB-US
                locale_code: en_US # optional - from v4 it will be mandatory
                public_api_key: public-key
                private_api_key: private-key
        pages:
            -
                id: 'Homepage'
                type: 'home'
                routeName: 'sylius_shop_homepage'
                routeParameters: []
                title: 'Homepage'
                text: 'Welcome to our store!'
                image: null
```

Where every entry in the `stores` key must contain the Sylius channel code in `channel_code`, the locale code in `locale_code` and the related Clerk's public/private API key in `public_api_key` and  `private_api_key`.

## Sync your data with Clerk.io

Login into your Clerk.io [dashboard](https://my.clerk.io/) and go to the **Data** page. In the **Data Sync Settings** section, select **Clerk.io JSON Feed V2** as **Sync Method** and enter the following JSON Feed URL
structure for each resource entry:

```
https://your-sylius-store.com/clerk/feed/channelCode/localeCode/[products|categories|orders|customers|pages]
```

Where `https://your-sylius-store.com` is your Sylius store base URL, `channelCode` is the database code of the Sylius channel you want to sync and `localeCode` is the locale code of the locale you want to sync.

For example, if you have a channel with code `WEB-US` and a locale with code `en_US`, you can use the following JSON Feed URLs:

```
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/products
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/categories
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/orders
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/customers
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/pages
```

NOTE: you can also use the query string parameters to filter the feed content. By default the controller checks for the following keys:
- `limit` and `offset` for pagination
- `modified_after` to get only the entities modified after a certain date (in the Incremental time field you should put the number of hours since the last sync)

Example:
```
https://your-sylius-store.com/clerk/feed/WEB-US/en_US/products?limit={{limit}}offset={{offset}}&modified_after={{modified_after}}
```

Or set up a cronjob for this command:

```bash
bin/console webgriffe:clerk:feed:generate
```

and use the following JSON Feed URL:

```
https://your-sylius-store.com/feed/clerk.io/<channelCode>/<localeCode>/[products|categories|orders|customers|pages]/all.json
```

## Install Clerk.js on you storefront

Like stated in the official Clerk
documentation [here](https://docs.clerk.io/docs/clerkjs-quick-start#section-installing-clerkjs), you have to put the
Clerk.js tracking code on all pages of your store just before the `</head>` tag. To do so this plugin expose a dedicated
controller action which you can render in your Twig template, for example like the following:

```twig
{# templates/bundles/SyliusShopBundle/layout.html.twig #}

{% extends '@!SyliusShop/layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}

    {{ render(url('webgriffe_sylius_clerk_tracking_code')) }}
{% endblock %}
```

From then, you can use all the Clerk.js features on your store pages.

## Install sales tracking on order success page

Like stated in the official Clerk
documentation [here](https://help.clerk.io/en/articles/1130393-installing-sales-tracking-on-other-custom-platforms), you
should optimise Clerk.io by installing the instant sales tracking code on your thank you page. To do so this plugin
expose a dedicated controller action which you can render in your thank you page Twig template, for example like the
following:

```twig
{# templates/bundles/SyliusShopBundle/Order/thankYou.html.twig #}
{% extends '@!SyliusShop/Order/thankYou.html.twig' %}

{% block javascripts %}
    {{ parent() }}

    {{ render(url('webgriffe_sylius_clerk_sales_tracking', {orderId: order.id})) }}
{% endblock %}
```
