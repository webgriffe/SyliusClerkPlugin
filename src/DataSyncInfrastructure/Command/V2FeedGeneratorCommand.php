<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Command;

if (!interface_exists(\Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Repository\RepositoryInterface::class, \Sylius\Resource\Doctrine\Persistence\RepositoryInterface::class);
}
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Enum\Resource;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Generator\FeedGeneratorInterface;
use Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\ValueObject\Feed;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[AsCommand(name: 'webgriffe:clerk:feed:generate', description: 'Generate feeds for Clerk.io data sync')]
class V2FeedGeneratorCommand extends Command
{
    use LockableTrait;

    private SymfonyStyle $io;

    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly RepositoryInterface $localeRepository,
        private readonly FeedGeneratorInterface $productsFeedGenerator,
        private readonly FeedGeneratorInterface $categoriesFeedGenerator,
        private readonly FeedGeneratorInterface $customersFeedGenerator,
        private readonly FeedGeneratorInterface $ordersFeedGenerator,
        private readonly FeedGeneratorInterface $pagesFeedGenerator,
        private readonly Filesystem $filesystem,
        private readonly string $feedsStorageDirectory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate feeds for Clerk.io data sync.');
        $this->setHelp('The <info>%command.name%</info> command generates feeds for Clerk.io data sync.
You can specify the channels, locales and resources for which you want to generate feeds. If no options are specified, feeds for all channels, locales and resources will be generated.
If you specify multiple values for the same option, feeds for all the specified values will be generated.
If you specify an invalid value for an option, the command will fail.
Example usage:
<info>php %command.full_name% --channelCode=web --channelCode=mobile --localeCode=en_US --localeCode=it_IT --resource=products --resource=categories</info>
This command will only generate feeds for the web and mobile channels, for the en_US and it_IT locales, and for the products and categories resources.
');
        $this->addOption('channelCode', 'c', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'If specified, only feeds for the given channels will be generated.', []);
        $this->addOption('localeCode', 'l', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'If specified, only feeds for the given locale codes will be generated.', []);
        $this->addOption('resource', 'r', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'If specified, only feeds for the given resources will be generated.', []);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->writeln('Starting Clerk.io feed generation...');
        if (!$this->lock()) {
            $this->io->error('The command is already running in another process, quitting.');

            return Command::FAILURE;
        }
        $this->filesystem->mkdir($this->feedsStorageDirectory);

        /** @var iterable<string> $channelCodes */
        $channelCodes = $input->getOption('channelCode');
        if ($channelCodes !== []) {
            $channels = [];
            foreach ($channelCodes as $channelCode) {
                $channel = $this->channelRepository->findOneByCode($channelCode);
                if (!$channel instanceof ChannelInterface) {
                    $this->io->error(sprintf('Channel with code "%s" not found. Feeds not generated.', $channelCode));

                    return Command::FAILURE;
                }
                $channels[] = $channel;
            }
        } else {
            /** @var ChannelInterface[] $channels */
            $channels = $this->channelRepository->findAll();
        }
        /** @var iterable<string> $localeCodes */
        $localeCodes = $input->getOption('localeCode');
        if ($localeCodes !== []) {
            $availableLocales = [];
            foreach ($localeCodes as $localeCode) {
                $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);
                if (!$locale instanceof LocaleInterface) {
                    $this->io->error(sprintf('Locale with code "%s" not found. Feeds not generated.', $localeCode));

                    return Command::FAILURE;
                }
                $availableLocales[] = $locale->getCode();
            }
        } else {
            $locales = $this->localeRepository->findAll();
            $availableLocales = array_map(
                static fn (LocaleInterface $locale) => (string) $locale->getCode(),
                $locales,
            );
        }
        /** @var iterable<string> $resourceValues */
        $resourceValues = $input->getOption('resource');
        if ($resourceValues !== []) {
            $resources = [];
            foreach ($resourceValues as $resourceValue) {
                $resource = Resource::tryFrom($resourceValue);
                if ($resource === null) {
                    $this->io->error(sprintf('Resource with value "%s" not found. Feeds not generated.', $resourceValue));

                    return Command::FAILURE;
                }
                $resources[] = $resource;
            }
        } else {
            $resources = Resource::cases();
        }

        foreach ($channels as $channel) {
            $channelLocales = $channel->getLocales()->filter(
                static fn (LocaleInterface $locale) => in_array((string) $locale->getCode(), $availableLocales, true),
            );
            foreach ($channelLocales as $locale) {
                foreach ($resources as $resource) {
                    $feedGenerator = match ($resource) {
                        Resource::PRODUCTS => $this->productsFeedGenerator,
                        Resource::CATEGORIES => $this->categoriesFeedGenerator,
                        Resource::CUSTOMERS => $this->customersFeedGenerator,
                        Resource::ORDERS => $this->ordersFeedGenerator,
                        Resource::PAGES => $this->pagesFeedGenerator,
                    };
                    $feed = $feedGenerator->generate($channel, (string) $locale->getCode());
                    $feedFilePath = $this->getFeedFilePath($feed);

                    $this->io->writeln(sprintf('Writing feed to file: %s', $feedFilePath));
                    $this->filesystem->dumpFile($feedFilePath, $feed->getContent());
                }
            }
        }

        $this->io->success('Clerk.io feed generation completed successfully.');

        return Command::SUCCESS;
    }

    private function getFeedFilePath(Feed $productsFeed): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->feedsStorageDirectory, '/'),
            ltrim($productsFeed->getFileName(), '/'),
        );
    }
}
