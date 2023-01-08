<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ProcessCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'process')
            ->addArgument(name: 'runtime', mode: InputArgument::REQUIRED, description: 'The runtime (fpm or cli)')
            ->addOption(
                name: 'max-invocations',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The maximum number of invocations',
                default: 250,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return self::SUCCESS;
    }
}
