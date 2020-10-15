<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Suite\SuiteGenerator;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSuiteCommand extends BaseGenerateCommand
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:suite')
            ->setDescription('This command generates a single suite based on declaration in xml')
            ->addArgument(
                'suites',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'argument which indicates suite names for generation (separated by space)'
            );

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility
        $remove = $input->getOption('remove');
        $verbose = $output->isVerbose();
        $allowSkipped = $input->getOption('allow-skipped');

        // Set application configuration so we can references the user options in our framework
        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::GENERATION_PHASE,
            $verbose,
            $debug,
            $allowSkipped
        );

        // Remove previous GENERATED_DIR if --remove option is used
        if ($remove) {
            $this->removeGeneratedDirectory($output, $output->isVerbose());
        }

        $suites = $input->getArgument('suites');

        $generated = 0;
        foreach ($suites as $suite) {
            try {
                SuiteGenerator::getInstance()->generateSuite($suite);
                if ($output->isVerbose()) {
                    $output->writeLn("suite $suite generated");
                }
                $generated++;
            } catch (FastFailException $e) {
                throw $e;
            } catch (\Exception $e) {
            }
        }

        if (empty(GenerationErrorHandler::getInstance()->getAllErrors())) {
            if ($generated > 0) {
                $output->writeln("Suites Generated" . PHP_EOL);
                return 0;
            }
        } else {
            GenerationErrorHandler::getInstance()->printErrorSummary();
            if ($generated > 0) {
                $output->writeln("Suites Generated (with errors)" . PHP_EOL);
                return 1;
            }
        }

        $output->writeln("No Suite Generated" . PHP_EOL);
        return 1;
    }
}
