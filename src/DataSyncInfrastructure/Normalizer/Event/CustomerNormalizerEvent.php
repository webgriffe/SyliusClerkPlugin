<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerNormalizerEvent
{
    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      email: string,
     *      subscribed: bool,
     *      zip?: string,
     *      gender?: string,
     *      age?: int,
     *      is_b2b?: bool,
     *  } $customerData
     */
    public function __construct(
        private array $customerData,
        private readonly CustomerInterface $customer,
        private readonly ChannelInterface $channel,
        private readonly string $localeCode,
        private readonly array $context,
    ) {
    }

    /**
     * @return array{
     *      id: string|int,
     *      name: string,
     *      email: string,
     *      subscribed: bool,
     *      zip?: string,
     *      gender?: string,
     *      age?: int,
     *      is_b2b?: bool,
     *  }
     */
    public function getCustomerData(): array
    {
        return $this->customerData;
    }

    /**
     * @param array{
     *      id: string|int,
     *      name: string,
     *      email: string,
     *      subscribed: bool,
     *      zip?: string,
     *      gender?: string,
     *      age?: int,
     *      is_b2b?: bool,
     *  } $customerData
     */
    public function setCustomerData(array $customerData): void
    {
        $this->customerData = $customerData;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
