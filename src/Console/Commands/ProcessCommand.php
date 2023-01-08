<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WPFortress\Runtime\Contracts\LambdaRuntimeProcessorContract;

final class ProcessCommand extends Command
{
    public function __construct(
        private LambdaRuntimeProcessorContract $lambdaRuntimeProcessor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(name: 'process')
            ->addOption(
                name: 'with-fastcgi',
                mode: InputOption::VALUE_NONE,
                description: 'Whether to kickstart FastCGI',
            )
            ->addOption(
                name: 'max-invocations',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The maximum number of invocations',
                default: 250,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption(name: 'with-fastcgi') === true) {
            $this->lambdaRuntimeProcessor->startFastCGIProcess();
        }

        $maxInvocations = $input->getOption(name: 'max-invocations');
        for ($invocations = 0; $invocations < $maxInvocations; $invocations++) {
            $this->lambdaRuntimeProcessor->processNextInvocation();
        }

        return self::SUCCESS;
    }
}
