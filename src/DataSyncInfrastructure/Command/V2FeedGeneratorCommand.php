<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\DataSyncInfrastructure\Command;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
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
     */
    public function __construct(
        private readonly ChannelRepositoryInterface $channelRepository,
        private readonly FeedGeneratorInterface $productsFeedGenerator,
        private readonly Filesystem $filesystem,
        private readonly string $feedsStorageDirectory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
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
        /** @var ChannelInterface[] $channels */
        $channels = $this->channelRepository->findAll();
        foreach ($channels as $channel) {
            foreach ($channel->getLocales() as $locale) {
                $productsFeed = $this->productsFeedGenerator->generate($channel, (string) $locale->getCode());
                $feedFilePath = $this->getFeedFilePath($productsFeed);

                $this->io->writeln(sprintf('Writing feed to file: %s', $feedFilePath));
                $this->filesystem->dumpFile($feedFilePath, $productsFeed->getContent());
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
