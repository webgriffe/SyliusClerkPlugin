<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

final class ClerkJsonEncoder implements EncoderInterface
{
    public const FORMAT = 'clerk_json';

    /**
     * @var EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(EncoderInterface $jsonEncoder)
    {
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = [])
    {
        return $this->jsonEncoder->encode($data, 'json', $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format): bool
    {
        return self::FORMAT === $format;
    }
}
