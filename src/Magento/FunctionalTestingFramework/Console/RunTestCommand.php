<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class RunTestCommand extends BaseGenerateCommand
{
    /**
     * The return code. Determined by all tests that run.
     *
     * @var integer
     */
    private $returnCode = 0;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName("run:test")
            ->setDescription("generation and execution of test(s) defined in xml")
            ->addArgument(
                'name',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                "name of tests to generate and execute"
            )->addOption('skip-generate', 'k', InputOption::VALUE_NONE, "skip generation and execute existing test");

        parent::configure();
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tests = $input->getArgument('name');
        $skipGeneration = $input->getOption('skip-generate');
        $force = $input->getOption('force');
        $remove = $input->getOption('remove');
        $debug = $input->getOption('debug') ?? MftfApplicationConfig::LEVEL_DEVELOPER; // for backward compatibility
        $allowSkipped = $input->getOption('allowSkipped');

        if ($skipGeneration and $remove) {
            // "skip-generate" and "remove" options cannot be used at the same time
            throw new TestFrameworkException(
                "\"skip-generate\" and \"remove\" options can not be used at the same time."
            );
        }

        $testConfiguration = $this->getTestAndSuiteConfiguration($tests);

        if (!$skipGeneration) {
            $command = $this->getApplication()->find('generate:tests');
            $args = [
                '--tests' => $testConfiguration,
                '--force' => $force,
                '--remove' => $remove,
                '--debug' => $debug,
                '--allowSkipped' => $allowSkipped
            ];
            $command->run(new ArrayInput($args), $output);
        }

        $testConfigArray = json_decode($testConfiguration, true);

        // run tests not referenced in suites
        $this->runTests($testConfigArray['tests'], $output);

        // run tests in suites
        $this->runTestsInSuite($testConfigArray['suites'], $output);

        return $this->returnCode;

    }

    /**
     * Run tests not referenced in suites
     * @param array $testsConfig
     * @param OutputInterface $output
     * @throws TestFrameworkException
     */
    private function runTests($testsConfig, OutputInterface $output) {


        $tests = $testsConfig ?? [];
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional ';
        $testsDirectory = TESTS_MODULE_PATH .
            DIRECTORY_SEPARATOR .
            TestGenerator::GENERATED_DIR .
            DIRECTORY_SEPARATOR .
            TestGenerator::DEFAULT_DIR .
            DIRECTORY_SEPARATOR ;

        foreach ($tests as $test) {
            $testName = $test . 'Cest.php';
            if (!realpath($testsDirectory . $testName)) {
                throw new TestFrameworkException(
                    $testName . " is not available under " . $testsDirectory
                );
            }
            $fullCommand = $codeceptionCommand . $testsDirectory . $testName . ' --verbose --steps';
            $process = new Process($fullCommand);
            $process->setWorkingDirectory(TESTS_BP);
            $process->setIdleTimeout(600);
            $process->setTimeout(0);
            $subReturnCode = $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
            $this->returnCode = max($this->returnCode, $subReturnCode);
        }
    }

    /**
     * Run tests referenced in suites within suites' context.
     * @param array $suitesConfig
     * @param OutputInterface $output
     */
    private function runTestsInSuite($suitesConfig, OutputInterface $output) {

        $suites = $suitesConfig ?? [];
        $codeceptionCommand = realpath(PROJECT_ROOT . '/vendor/bin/codecept') . ' run functional --verbose --steps ';
        $testGroups = array_keys($suites);
        //for tests in suites, run them as a group to run before and after block
        foreach ($testGroups as $testGroup) {
            $fullCommand = $codeceptionCommand . " -g {$testGroup}";
            $process = new Process($fullCommand);
            $process->setWorkingDirectory(TESTS_BP);
            $process->setIdleTimeout(600);
            $process->setTimeout(0);
            $subReturnCode = $process->run(function ($type, $buffer) use ($output) {
                $output->write($buffer);
            });
            $this->returnCode = max($this->returnCode, $subReturnCode);
        }
    }
}
