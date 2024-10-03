<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\ValueObject;

use Sylius\Component\Core\Model\ChannelInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webmozart\Assert\Assert;

final readonly class Feed
{
    public function __construct(
        private Resource $resource,
        private string $content,
        private ChannelInterface $channel,
        private string $localeCode,
        private ?\DateTimeInterface $modifiedAfter = null,
        private ?int $limit = null,
        private ?int $offset = null,
    ) {
    }

    public function getResource(): Resource
    {
        return $this->resource;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getModifiedAfter(): ?\DateTimeInterface
    {
        return $this->modifiedAfter;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getFileName(): string
    {
        $channelCode = $this->getChannel()->getCode();
        Assert::stringNotEmpty($channelCode, 'Channel code must be set.');

        $dir = sprintf(
            '%s/%s/%s/',
            $channelCode,
            $this->getLocaleCode(),
            strtolower($this->getResource()->name),
        );

        if ($this->getModifiedAfter() === null && $this->getLimit() === null && $this->getOffset() === null) {
            return $dir . 'all.json';
        }

        $fileName = '';
        if ($this->getModifiedAfter() !== null) {
            $fileName .= 'modified_after_' . $this->getModifiedAfter()->format(\DateTime::ATOM);
        }
        if ($this->getLimit() !== null) {
            if ($this->getOffset() !== null) {
                $fileName .= sprintf(
                    '%sfrom_%s_to_%s',
                    $fileName === '' ? '' : '_',
                    $this->getOffset(),
                    $this->getOffset() + $this->getLimit(),
                );
            } else {
                $fileName .= sprintf(
                    '%sto_%s',
                    $fileName === '' ? '' : '_',
                    $this->getLimit(),
                );
            }
        } elseif ($this->getOffset() !== null) {
            $fileName .= sprintf(
                '%sfrom_%s',
                $fileName === '' ? '' : '_',
                $this->getOffset(),
            );
        }

        return sprintf(
            '%s%s.json',
            $dir,
            $fileName,
        );
    }
}
