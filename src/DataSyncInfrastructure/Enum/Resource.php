<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum;

enum Resource
{
    case PRODUCTS;
    case CATEGORIES;
    case ORDERS;
    case CUSTOMERS;
    case PAGES;
}
