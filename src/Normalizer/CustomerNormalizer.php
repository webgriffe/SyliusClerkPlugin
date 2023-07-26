<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Normalizer;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGenerator;

final class CustomerNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = [])
    {
        if (!$object instanceof CustomerInterface) {
            throw new InvalidArgumentException('This normalizer supports only instances of ' . CustomerInterface::class);
        }
        $customer = $object;

        return [
            'id' => $customer->getId(),
            'name' => $customer->getFullName(),
            'email' => $customer->getEmail(),
            'gender' => $customer->getGender(),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof CustomerInterface && $format === FeedGenerator::NORMALIZATION_FORMAT;
    }
}
