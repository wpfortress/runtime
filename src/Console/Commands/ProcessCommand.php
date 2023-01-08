<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use WPFortress\Runtime\Contracts\FastCGIProcessManagerContract;
use WPFortress\Runtime\Contracts\InvocationHandlerBusContract;
use WPFortress\Runtime\Lambda\RuntimeClient;

final class ProcessCommand extends Command
{
    public function __construct(
        private FastCGIProcessManagerContract $processManager,
        private RuntimeClient $runtimeClient,
        private InvocationHandlerBusContract $handlerBus,
    ) {
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
        if ($input->getArgument(name: 'runtime') === 'fpm') {
            try {
                $this->processManager->start();
            } catch (Throwable $exception) {
                $this->runtimeClient->sendInitialisationError($exception);

                return self::FAILURE;
            }
        }

        $invocations = 0;
        $maxInvocations = $input->getOption(name: 'max-invocations');

        while (true) {
            $invocation = $this->runtimeClient->retrieveNextInvocation();

            try {
                $this->runtimeClient->sendInvocationResponse(
                    invocation: $invocation,
                    response: $this->handlerBus->handle(invocation: $invocation)->handle(invocation: $invocation),
                );

                ++$invocations;
            } catch (Throwable $exception) {
                $this->runtimeClient->sendInvocationError($invocation, $exception);
            }

            if ($invocation >= $maxInvocations) {
                return self::SUCCESS;
            }
        }
    }
}
