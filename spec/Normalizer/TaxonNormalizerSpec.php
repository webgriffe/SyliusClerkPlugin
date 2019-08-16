<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Normalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Normalizer\TaxonNormalizer;

class TaxonNormalizerSpec extends ObjectBehavior
{
    function let(RouterInterface $router, TaxonRepositoryInterface $taxonRepository)
    {
        $this->beConstructedWith($router, $taxonRepository);
    }

    function it_is_initializable_and_implements_normalizer_interface()
    {
        $this->shouldHaveType(TaxonNormalizer::class);
        $this->shouldHaveType(NormalizerInterface::class);
    }

    function it_supports_taxon_normalization_for_clerk_array_format()
    {
        $this->supportsNormalization(new Taxon(), 'clerk_array')->shouldReturn(true);
    }

    function it_does_not_support_taxon_normalization_for_other_formats()
    {
        $this->supportsNormalization(new Taxon(), 'json')->shouldReturn(false);
        $this->supportsNormalization(new Taxon(), 'other_format')->shouldReturn(false);
    }

    function it_does_not_support_other_object_normalization()
    {
        $this->supportsNormalization(new \stdClass(), 'clerk_array')->shouldReturn(false);
    }
}
