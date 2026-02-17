<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Normalizer\Event\CustomerNormalizerEvent;
use Webmozart\Assert\Assert;

final readonly class CustomerNormalizer implements NormalizerInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param CustomerInterface|mixed $data
     *
     * @return array{
     *     id: string|int,
     *     name: string,
     *     email: string,
     *     subscribed: bool,
     * }&array<string, mixed>
     */
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $customer = $data;
        Assert::isInstanceOf($customer, CustomerInterface::class);
        $channel = $context['channel'];
        Assert::isInstanceOf($channel, ChannelInterface::class, 'The given context should contain a ChannelInterface instance.');
        $localeCode = $context['localeCode'];
        Assert::stringNotEmpty($localeCode, 'The given context should contain a non-empty string localeCode.');

        $customerId = $customer->getId();
        if (!is_string($customerId) && !is_int($customerId)) {
            throw new \InvalidArgumentException('Customer ID must be a string or an integer, "' . gettype($customerId) . '" given.');
        }

        $customerName = $customer->getFullName();
        if ($customerName === '') {
            throw new \InvalidArgumentException('Customer name must not be empty for customer with ID "' . $customerId . '".');
        }
        $customerEmail = $customer->getEmail();
        if ($customerEmail === null) {
            throw new \InvalidArgumentException('Customer email must not be null for customer with ID "' . $customerId . '".');
        }

        $customerData = [
            'id' => $customerId,
            'name' => $customerName,
            'email' => $customerEmail,
            'subscribed' => $customer->isSubscribedToNewsletter(),
        ];

        $customerNormalizerEvent = new CustomerNormalizerEvent(
            $customerData,
            $customer,
            $channel,
            $localeCode,
            $context,
        );
        $this->eventDispatcher->dispatch($customerNormalizerEvent);

        return $customerNormalizerEvent->getCustomerData();
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CustomerInterface &&
            $format === 'array' &&
            array_key_exists('type', $context) &&
            $context['type'] === 'webgriffe_sylius_clerk_plugin'
        ;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            CustomerInterface::class => true,
        ];
    }
}
