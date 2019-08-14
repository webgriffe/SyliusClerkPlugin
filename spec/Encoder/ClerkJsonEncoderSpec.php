<?php

declare(strict_types=1);

namespace spec\Webgriffe\SyliusClerkPlugin\Encoder;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Webgriffe\SyliusClerkPlugin\Encoder\ClerkJsonEncoder;

class ClerkJsonEncoderSpec extends ObjectBehavior
{
    function let(EncoderInterface $jsonEncoder)
    {
        $this->beConstructedWith($jsonEncoder);
    }

    function it_is_initializable_and_implements_encoder_interface()
    {
        $this->shouldHaveType(ClerkJsonEncoder::class);
        $this->shouldHaveType(EncoderInterface::class);
    }

    function it_supports_clerk_json_format()
    {
        $this->supportsEncoding('clerk_json')->shouldReturn(true);
    }

    function it_does_not_support_other_formats()
    {
        $this->supportsEncoding('json')->shouldReturn(false);
        $this->supportsEncoding('xml')->shouldReturn(false);
        $this->supportsEncoding('yaml')->shouldReturn(false);
        $this->supportsEncoding('csv')->shouldReturn(false);
    }

    function it_delegates_encoding_to_the_json_encoder(EncoderInterface $jsonEncoder)
    {
        $encodedValue = '{"array": "of", "data": []}';
        $jsonEncoder->encode(['array' => 'of', 'data' => []], 'json', [])->willReturn($encodedValue);
        $this->encode(['array' => 'of', 'data' => []], 'clerk_json')->shouldReturn($encodedValue);
    }
}
