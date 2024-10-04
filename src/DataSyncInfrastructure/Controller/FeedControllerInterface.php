<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface FeedControllerInterface
{
    public function __invoke(
        string $channelCode,
        string $localeCode,
        string $resourceValue,
        Request $request,
    ): Response;
}
