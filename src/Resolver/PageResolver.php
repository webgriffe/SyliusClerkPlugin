<?php
declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Resolver;

final class PageResolver implements PageResolverInterface
{
    public function createPageList(): array
    {
        // Create your own resolver here
        // This is an example of how to create a page list
        //$pages = [
        //    [
        //        "id"    => 135,
        //        "type"  => "cms",
        //        "url"   => "https://galactic-empire-merch.com/imperial-goods/tatooine",
        //        "title" => "Open Hours",
        //        "text"  => "The main text about our opening hours...",
        //    ],
        //    [
        //        "id"       => 1354,
        //        "type"     => "blog",
        //        "url"      => "https://galactic-empire-merch.com/imperial-goods/tatooine",
        //        "title"    => "New Blog Post",
        //        "text"     => "The main text about our opening hours...",
        //        "keywords" => ["blog", "post", "new"],
        //    ],
        //];

        return [];
    }
}
