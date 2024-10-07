<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Client\Response;

final readonly class Verify
{
    public function __construct(
        public string $status,
        public array $data,
    ) {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isValid(): bool
    {
        return $this->getStatus() === 'ok';
    }
}
