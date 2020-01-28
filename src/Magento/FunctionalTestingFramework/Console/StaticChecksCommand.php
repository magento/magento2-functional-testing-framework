<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\StaticCheck\StaticCheckInterface;
use Magento\FunctionalTestingFramework\StaticCheck\StaticChecksList;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class StaticChecksCommand extends Command
{
    /**
     * Pool of all existing static check objects
     *
     * @var StaticCheckInterface[]
     */
    private $allStaticCheckObjects;

    /**
     * Static checks to run
     *
     * @var StaticCheckInterface[]
     */
    private $staticCheckObjects;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $list = new StaticChecksList();
        $this->allStaticCheckObjects = $list->getStaticChecks();
        $staticCheckNames = implode(', ', array_keys($this->allStaticCheckObjects));
        $description = "This command will run all static checks on xml test materials. "
            . "Available static check scripts are:\n{$staticCheckNames}";
        $this->setName('static-checks')
            ->setDescription($description)
            ->addArgument(
                'names',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'name(s) of specific static check script(s) to run'
            );
    }

    /**
     * Run required static check scripts
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->validateInputArguments($input, $output);
        } catch (InvalidArgumentException $e) {
            LoggingUtil::getInstance()->getLogger(StaticChecksCommand::class)->error($e->getMessage());
            $output->writeln($e->getMessage() . " Please fix input arguments and rerun.");
            return 1;
        }

        $errors = [];
        foreach ($this->staticCheckObjects as $name => $staticCheck) {
            LoggingUtil::getInstance()->getLogger(get_class($staticCheck))->info(
                "\nRunning static check script for: " . $name
            );
            $output->writeln(
                "\nRunning static check script for: " . $name
            );

            $staticCheck->execute($input);

            $staticOutput = $staticCheck->getOutput();
            LoggingUtil::getInstance()->getLogger(get_class($staticCheck))->info($staticOutput);
            $output->writeln($staticOutput);
            $errors += $staticCheck->getErrors();
        }

        if (empty($errors)) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Validate input arguments
     *
     * @param InputInterface $input
     * @return void
     * @throws InvalidArgumentException
     */
    private function validateInputArguments(InputInterface $input)
    {
        $this->staticCheckObjects = [];
        $requiredChecksNames = $input->getArgument('names');
        $invalidCheckNames = [];
        // Found user required static check script(s) to run,
        // If no static check name is supplied, run all static check scripts
        if (empty($requiredChecksNames)) {
            $this->staticCheckObjects = $this->allStaticCheckObjects;
        } else {
            for ($index = 0; $index < count($requiredChecksNames); $index++) {
                if (in_array($requiredChecksNames[$index], array_keys($this->allStaticCheckObjects))) {
                    $this->staticCheckObjects[$requiredChecksNames[$index]] =
                        $this->allStaticCheckObjects[$requiredChecksNames[$index]];
                } else {
                    $invalidCheckNames[] = $requiredChecksNames[$index];
                }
            }
        }

        if (!empty($invalidCheckNames)) {
            throw new InvalidArgumentException(
                "Invalid static check script(s): " . implode(', ', $invalidCheckNames) . "."
            );
        }
    }
}
