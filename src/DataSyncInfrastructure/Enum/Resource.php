<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum;

enum Resource: string
{
    case PRODUCTS = 'products';
    case CATEGORIES = 'categories';
    case ORDERS = 'orders';
    case CUSTOMERS = 'customers';
    case PAGES = 'pages';
}
