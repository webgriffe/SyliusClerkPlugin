<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Service;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CategoriesFeedGenerator
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(TaxonRepositoryInterface $taxonRepository, SerializerInterface $serializer)
    {
        $this->taxonRepository = $taxonRepository;
        $this->serializer = $serializer;
    }

    public function generate(ChannelInterface $channel): string
    {
        $taxons = [];
        $queryBuilder = $this->taxonRepository->createListQueryBuilder();
        /** @var TaxonInterface $taxon */
        foreach ($queryBuilder->getQuery()->getResult() as $taxon) {
            $taxons[] = $taxon;
        }

        return $this->serializer->serialize($taxons, 'json', ['channel' => $channel]);
    }
}
