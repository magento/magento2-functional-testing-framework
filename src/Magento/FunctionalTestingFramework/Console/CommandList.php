<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Console;

/**
 * Class CommandList has a list of commands.
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class CommandList implements CommandListInterface
{
    /**
     * List of Commands
     * @var \Symfony\Component\Console\Command\Command[]
     */
    private $commands;

    /**
     * Constructor
     *
     * @param array $commands
     */
    public function __construct(array $commands = [])
    {
        $this->commands = [
            'build:project' => new BuildProjectCommand(),
            'codecept:run' => new CodeceptRunCommand(),
            'doctor' => new DoctorCommand(),
            'generate:suite' => new GenerateSuiteCommand(),
            'generate:tests' => new GenerateTestsCommand(),
            'generate:urn-catalog' => new GenerateDevUrnCommand(),
            'reset' => new CleanProjectCommand(),
            'generate:failed' => new GenerateTestFailedCommand(),
            'run:failed' => new RunTestFailedCommand(),
            'run:group' => new RunTestGroupCommand(),
            'run:manifest' => new RunManifestCommand(),
            'run:test' => new RunTestCommand(),
            'setup:env' => new SetupEnvCommand(),
            'static-checks' => new StaticChecksCommand(),
            'upgrade:tests' => new UpgradeTestsCommand(),
        ] + $commands;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
