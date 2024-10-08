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

/**
 * @deprecated This class is deprecated and will be removed in the next major version. Use the new Clerk feed v2 normalizer instead.
 */
final class TaxonNormalizer implements NormalizerInterface
{
    public function __construct(
        private RouterInterface $router,
        private TaxonRepositoryInterface $taxonRepository,
    ) {
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, TaxonInterface::class);
        Assert::isInstanceOf($context['channel'], ChannelInterface::class);
        $taxon = $object;
        $channel = $context['channel'];
        $locale = $channel->getDefaultLocale();
        Assert::isInstanceOf($locale, LocaleInterface::class);
        $taxonTranslation = $taxon->getTranslation($locale->getCode());
        $subcategories = [];
        if ($taxon->getCode() !== null) {
            $subcategories = array_map(
                function (TaxonInterface $taxon) {
                    return $taxon->getId();
                },
                $this->taxonRepository->findChildren($taxon->getCode(), $locale->getCode()),
            );
        }

        return [
            'id' => $taxon->getId(),
            'name' => $taxonTranslation->getName(),
            'url' => $this->router->generate(
                'sylius_shop_product_index',
                ['slug' => $taxonTranslation->getSlug(), '_locale' => $locale->getCode()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
            'subcategories' => $subcategories,
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof TaxonInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
    }
}
