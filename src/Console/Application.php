<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Traversable;

final class Application extends BaseApplication
{
    public const NAME = 'WPFortress Runtime';

    public const DEFAULT_COMMAND = 'process';

    /** @param iterable<Command> $commands */
    public function __construct(iterable $commands)
    {
        parent::__construct(name: self::NAME);

        $this->addCommands(commands: $commands instanceof Traversable ? iterator_to_array($commands) : $commands);
        $this->setDefaultCommand(commandName: self::DEFAULT_COMMAND, isSingleCommand: true);
    }
}
