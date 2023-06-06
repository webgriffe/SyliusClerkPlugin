<?php

declare(strict_types=1);

namespace Webgriffe\SyliusClerkPlugin\Command;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use Webgriffe\SyliusClerkPlugin\Service\FeedGeneratorInterface;
use Webmozart\Assert\Assert;

final class FeedGeneratorCommand extends Command
{
    use LockableTrait;

    protected static $defaultName = 'webgriffe:clerk:generate-feed';

    protected bool $hasError = false;

    private ?OutputInterface $output = null;

    public function __construct(
        private FeedGeneratorInterface $feedGenerator,
        private ChannelRepositoryInterface $channelRepository,
        private RouterInterface $router,
        private string $projectDir,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generate JSON feed for Clerk.io');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->output('Start command', [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);
        if (!$this->lock()) {
            $this->output('The command is already running in another process.');

            return Command::FAILURE;
        }
        $this->output('Enable lock', [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);

        $this->output('Generating JSON feed in memory', [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);
        $channel = $this->channelRepository->findOneBy(['code' => 'ecommerce']);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        // avoid timeout
        set_time_limit(0);
        $this->router->getContext()->setHost($channel->getHostname() ?? 'localhost');

        $jsonFeed = $this->feedGenerator->generate($channel);

        $baseDir = rtrim($this->projectDir, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        $targetFile = $baseDir . 'var' . \DIRECTORY_SEPARATOR . 'storage' . \DIRECTORY_SEPARATOR . 'clerk_feed.json';
        $this->output(sprintf('Writing JSON feed to file "%s"', $targetFile), [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);
        file_put_contents($targetFile, $jsonFeed);

        $this->release();
        $this->output('Release lock', [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);

        $this->output('End command', [], LogLevel::DEBUG, OutputInterface::VERBOSITY_DEBUG);
        if ($this->hasError) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function output(
        string $message,
        array $context = [],
        string $level = LogLevel::INFO,
        int $verbosity = OutputInterface::OUTPUT_NORMAL,
    ): void {
        $this->logger->log($level, $message, $context, );

        $this->output?->writeln($message, $verbosity);
    }
}
