<?php
// @codingStandardsIgnoreFile
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BaseGenerateCommand
 * @package Magento\FunctionalTestingFramework\Console
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BaseGenerateCommand extends Command
{
    const MFTF_NOTICES = "Placeholder text for MFTF notices\n";
    const CODECEPT_RUN = 'codecept:run';
    const CODECEPT_RUN_FUNCTIONAL = self::CODECEPT_RUN . ' functional ';
    const CODECEPT_RUN_OPTION_NO_EXIT = ' --no-exit ';
    const FAILED_FILE = 'failed';

    /**
     * Enable pause()
     *
     * @var boolean
     */
    private $enablePause = null;

    /**
     * Full path to '_output' dir
     *
     * @var string
     */
    private $testsOutputDir = null;

    /**
     *  String contains all 'failed' tests
     *
     * @var string
     */
    private $allFailed;

    /**
     * Console output style
     *
     * @var SymfonyStyle
     */
    protected $ioStyle = null;

    /**
     * Full path to 'failed' file
     *
     * @var string
     */
    protected $testsFailedFile = null;

    /**
     * Configures the base command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'remove',
            'r',
            InputOption::VALUE_NONE,
            'remove previous generated suites and tests'
        )->addOption(
            "force",
            'f',
            InputOption::VALUE_NONE,
            'force generation and running of tests regardless of Magento Instance Configuration'
        )->addOption(
            "allow-skipped",
            'a',
            InputOption::VALUE_NONE,
            'Allows MFTF to generate and run skipped tests.'
        )->addOption(
            'debug',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Run extra validation when generating and running tests.',
            MftfApplicationConfig::LEVEL_DEFAULT
        );
    }

    /**
     * Remove GENERATED_DIR if exists when running generate:tests.
     *
     * @param OutputInterface $output
     * @param bool $verbose
     * @return void
     * @throws TestFrameworkException
     */
    protected function removeGeneratedDirectory(OutputInterface $output, bool $verbose)
    {
        $generatedDirectory = FilePathFormatter::format(TESTS_MODULE_PATH) . TestGenerator::GENERATED_DIR;

        if (file_exists($generatedDirectory)) {
            DirSetupUtil::rmdirRecursive($generatedDirectory);
            if ($verbose) {
                $output->writeln("removed files and directory $generatedDirectory");
            }
        }
    }

    /**
     * Returns an array of test configuration to be used as an argument for generation of tests
     * @param array $tests
     * @return false|string
     * @throws FastFailException
     */
    protected function getTestAndSuiteConfiguration(array $tests)
    {
        $testConfiguration['tests'] = null;
        $testConfiguration['suites'] = null;
        $testsReferencedInSuites = SuiteObjectHandler::getInstance()->getAllTestReferences();
        $suiteToTestPair = [];

        foreach($tests as $test) {
            if (strpos($test, ':') !== false) {
                $suiteToTestPair[] = $test;
                continue;
            }
            if (array_key_exists($test, $testsReferencedInSuites)) {
                $suites = $testsReferencedInSuites[$test];
                foreach ($suites as $suite) {
                    $suiteToTestPair[] = "$suite:$test";
                }
            }
            // configuration for tests
            else {
                $testConfiguration['tests'][] = $test;
            }
        }
        // configuration for suites
        foreach ($suiteToTestPair as $pair) {
            list($suite, $test) = explode(":", $pair);
            $testConfiguration['suites'][$suite][] = $test;
        }
        $testConfigurationJson = json_encode($testConfiguration);
        return $testConfigurationJson;
    }

    /**
     * Returns an array of test configuration to be used as an argument for generation of tests
     * This function uses group or suite names for generation
     * @return false|string
     * @throws FastFailException
     * @throws TestFrameworkException
     */
    protected function getGroupAndSuiteConfiguration(array $groupOrSuiteNames)
    {
        $result['tests'] = [];
        $result['suites'] = [];

        $groups = [];
        $suites = [];

        $allSuites = SuiteObjectHandler::getInstance()->getAllObjects();
        $testsInSuites = SuiteObjectHandler::getInstance()->getAllTestReferences();

        foreach ($groupOrSuiteNames as $groupOrSuiteName) {
            if (array_key_exists($groupOrSuiteName, $allSuites)) {
                $suites[] = $groupOrSuiteName;
            } else {
                $groups[] = $groupOrSuiteName;
            }
        }

        foreach ($suites as $suite) {
            $result['suites'][$suite] = [];
        }

        foreach ($groups as $group) {
            $testsInGroup = TestObjectHandler::getInstance()->getTestsByGroup($group);

            $testsInGroupAndNotInAnySuite = array_diff(
                array_keys($testsInGroup),
                array_keys($testsInSuites)
            );

            $testsInGroupAndInAnySuite = array_diff(
                array_keys($testsInGroup),
                $testsInGroupAndNotInAnySuite
            );

            foreach ($testsInGroupAndInAnySuite as $testInGroupAndInAnySuite) {
                $suiteName = $testsInSuites[$testInGroupAndInAnySuite][0];
                if (array_search($suiteName, $suites) !== false) {
                    // Suite is already being called to run in its entirety, do not filter list
                    continue;
                }
                $result['suites'][$suiteName][] = $testInGroupAndInAnySuite;
            }

            $result['tests'] = array_merge(
                $result['tests'],
                $testsInGroupAndNotInAnySuite
            );
        }

        if (empty($result['tests'])) {
            $result['tests'] = null;
        }
        if (empty($result['suites'])) {
            $result['suites'] = null;
        }

        $json = json_encode($result);
        return $json;
    }

    /**
     * Set Symfony IO Style
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function setIOStyle(InputInterface $input, OutputInterface $output)
    {
        // For IO style
        if (null === $this->ioStyle) {
            $this->ioStyle = new SymfonyStyle($input, $output);
        }
    }

    /**
     * Show predefined global notice messages
     *
     * @param OutputInterface $output
     * @return void
     */
    protected function showMftfNotices(OutputInterface $output)
    {
        if (null !== $this->ioStyle) {
            $this->ioStyle->note(self::MFTF_NOTICES);
        } else {
            $output->writeln(self::MFTF_NOTICES);
        }
    }

    /**
     * Return if pause() is enabled
     *
     * @return boolean
     */
    protected function pauseEnabled()
    {
        if (null === $this->enablePause) {
            if (getenv('ENABLE_PAUSE') === 'true') {
                $this->enablePause = true;
            } else {
                $this->enablePause = false;
            }
        }
        return $this->enablePause;
    }

    /**
     * Runs the bin/mftf codecept:run command and returns exit code
     *
     * @param string          $commandStr
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     */
    protected function codeceptRunTest(string $commandStr, OutputInterface $output)
    {
        $input = new StringInput($commandStr);
        $command = $this->getApplication()->find(self::CODECEPT_RUN);
        return $command->run($input, $output);
    }

    /**
     * Return tests _output directory
     *
     * @return string
     * @throws TestFrameworkException
     */
    protected function getTestsOutputDir()
    {
        if (!$this->testsOutputDir) {
            $this->testsOutputDir = FilePathFormatter::format(TESTS_BP) .
                "tests" .
                DIRECTORY_SEPARATOR .
                "_output" .
                DIRECTORY_SEPARATOR;
        }

        return $this->testsOutputDir;
    }

    /**
     * Save 'failed' tests
     *
     * @return void
     */
    protected function appendRunFailed()
    {
        try {
            if (!$this->testsFailedFile) {
                $this->testsFailedFile = $this->getTestsOutputDir() . self::FAILED_FILE;
            }

            if (file_exists($this->testsFailedFile)) {
                // Save 'failed' tests
                $contents = file_get_contents($this->testsFailedFile);
                if ($contents !== false && !empty($contents)) {
                    $this->allFailed .= trim($contents) . PHP_EOL;
                }
            }
        } catch (TestFrameworkException $e) {
        }
    }

    /**
     * Apply 'allFailed' in 'failed' file
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function applyAllFailed()
    {
        try {
            if (!$this->testsFailedFile) {
                $this->testsFailedFile = $this->getTestsOutputDir() . self::FAILED_FILE;
            }

            if (!empty($this->allFailed)) {
                // Update 'failed' with content from 'allFailed'
                if (file_exists($this->testsFailedFile)) {
                    rename($this->testsFailedFile, $this->testsFailedFile . '.copy');
                }
                if (file_put_contents($this->testsFailedFile, $this->allFailed) === false
                    && file_exists($this->testsFailedFile . '.copy')) {
                    rename($this->testsFailedFile . '.copy', $this->testsFailedFile);
                }
                if (file_exists($this->testsFailedFile . '.copy')) {
                    unlink($this->testsFailedFile . '.copy');
                }
            }
        } catch (TestFrameworkException $e) {
        }
    }
    /**
     * Codeception creates default xml file with name report.xml .
     * This function renames default file name with name of the test.
     *
     * @param string $xml
     * @param string $fileName
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    public function movingXMLFileFromSourceToDestination($xml, $fileName, $output)
    {
        if(!empty($xml) && file_exists($this->getTestsOutputDir().'report.xml')) {
            if (!file_exists($this->getTestsOutputDir().'xml')) {
                mkdir($this->getTestsOutputDir().'xml' , 0777, true);
            }
            $fileName = str_replace("Cest.php", "",$fileName);
            $existingFileName = $this->getTestsOutputDir().'report.xml';
            $newFileName = $this->getTestsOutputDir().'xml/'.$fileName.'_report.xml';
            $output->writeln( "<info>".sprintf(" report.xml file is moved to  ".
                    $this->getTestsOutputDir().'xml/'. ' location with the new name '.$fileName.'_report.xml')."</info>") ;
            rename($existingFileName , $newFileName);
        }
    }

}
