<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;
use Webmozart\Assert\Assert;

final class TaxonNormalizer implements NormalizerInterface
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    public function __construct(RouterInterface $router, TaxonRepositoryInterface $taxonRepository)
    {
        $this->router = $router;
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, TaxonInterface::class);
        Assert::isInstanceOf($context['channel'], ChannelInterface::class);
        /** @var TaxonInterface $taxon */
        $taxon = $object;
        /** @var ChannelInterface $channel */
        $channel = $context['channel'];
        $locale = $channel->getDefaultLocale();
        Assert::isInstanceOf($locale, LocaleInterface::class);
        $taxonTranslation = $taxon->getTranslation($locale->getCode());

        return [
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
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof TaxonInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
    }
}
