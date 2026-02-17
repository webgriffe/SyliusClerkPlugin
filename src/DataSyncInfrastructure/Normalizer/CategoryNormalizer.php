<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\CategoryNormalizerEvent;
use Webmozart\Assert\Assert;

final readonly class CategoryNormalizer implements NormalizerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @param TaxonInterface|mixed $data
     *
     * @return array{
     *     id: string|int,
     *     name: string,
     *     url: string,
     *     subcategories: array<int|string>,
     * }&array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $taxon = $data;
        Assert::isInstanceOf($taxon, TaxonInterface::class);
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The given context should contain a ChannelInterface instance.');
        $localeCode = $context['localeCode'];
        Assert::stringNotEmpty($localeCode, 'The given context should contain a non-empty string localeCode.');

        $taxonTranslation = $taxon->getTranslation($localeCode);

        $taxonId = $taxon->getId();
        if (!is_string($taxonId) && !is_int($taxonId)) {
            throw new \InvalidArgumentException('Taxon ID must be a string or an integer, "' . gettype($taxonId) . '" given.');
        }

        $taxonName = $taxonTranslation->getName();
        if ($taxonName === null) {
            throw new \InvalidArgumentException('Taxon name must not be null for the taxon "' . $taxonId . '".');
        }
        $taxonDescription = $taxonTranslation->getDescription();

        $taxonUrl = $this->getUrlOfTaxon($taxonTranslation, $channel);

        $categoryData = [
            'id' => $taxonId,
            'name' => $taxonName,
            'url' => $taxonUrl,
            'subcategories' => $this->getSubcategoryIds($taxon),
        ];
        if ($taxonDescription !== null) {
            $categoryData['description'] = $taxonDescription;
        }

        $categoryNormalizerEvent = new CategoryNormalizerEvent(
            $categoryData,
            $taxon,
            $channel,
            $localeCode,
            $context,
        );
        $this->eventDispatcher->dispatch($categoryNormalizerEvent);

        return $categoryNormalizerEvent->getCategoryData();
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof TaxonInterface &&
            $format === 'array' &&
            array_key_exists('type', $context) &&
            $context['type'] === 'webgriffe_sylius_clerk_plugin'
        ;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            TaxonInterface::class => true,
        ];
    }

    private function getUrlOfTaxon(TaxonTranslationInterface $taxonTranslation, ChannelInterface $channel): string
    {
        $channelRequestContext = $this->urlGenerator->getContext();
        $previousHost = $channelRequestContext->getHost();
        $channelHost = $channel->getHostname();
        Assert::string($channelHost);
        $channelRequestContext->setHost($channelHost);

        $url = $this->urlGenerator->generate(
            'sylius_shop_product_index',
            [
                'slug' => $taxonTranslation->getSlug(),
                '_locale' => $taxonTranslation->getLocale(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
        Assert::stringNotEmpty($url);

        $channelRequestContext->setHost($previousHost);

        return $url;
    }

    /**
     * @return array<int|string>
     */
    private function getSubcategoryIds(TaxonInterface $taxon): array
    {
        $categoryIds = [];
        foreach ($taxon->getChildren() as $child) {
            $childId = $child->getId();
            if (!is_string($childId) && !is_int($childId)) {
                throw new \InvalidArgumentException('Taxon ID must be a string or an integer, "' . gettype($childId) . '" given.');
            }
            $categoryIds[] = $childId;
        }

        return $categoryIds;
    }
}
