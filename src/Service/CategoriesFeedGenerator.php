<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class CategoriesFeedGenerator
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(TaxonRepositoryInterface $taxonRepository, RouterInterface $router)
    {
        $this->taxonRepository = $taxonRepository;
        $this->router = $router;
    }

    public function generate(ChannelInterface $channel): array
    {
        $locale = $channel->getDefaultLocale();
        Assert::isInstanceOf($locale, LocaleInterface::class);
        $categories = [];
        $queryBuilder = $this->taxonRepository->createListQueryBuilder();
        /** @var TaxonInterface $taxon */
        foreach ($queryBuilder->getQuery()->getResult() as $taxon) {
            $taxonTranslation = $taxon->getTranslation($locale->getCode());
            $category = [
                'id' => $taxon->getId(),
                'name' => $taxonTranslation->getName(),
                'url' => $this->router->generate(
                    'sylius_shop_product_index',
                    ['slug' => $taxonTranslation->getName(), '_locale' => $locale->getCode()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'subcategories' => array_map(
                    function (TaxonInterface $taxon) {
                        return $taxon->getId();
                    },
                    $this->taxonRepository->findChildren($taxon->getCode(), $locale->getCode())
                ),
            ];
            $categories[] = $category;
        }

        return $categories;
    }
}
