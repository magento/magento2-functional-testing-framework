<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;

class BaseGenerateCommand extends Command
{
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
            "allowSkipped",
            'a',
            InputOption::VALUE_NONE,
            'Allows MFTF to generate and run skipped tests.'
        )->addOption(
            'debug',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Run extra validation when generating and running tests. Use option \'none\' to turn off debugging -- 
             added for backward compatibility, will be removed in the next MAJOR release',
            MftfApplicationConfig::LEVEL_DEFAULT
        );
    }

    /**
     * Remove GENERATED_DIR if exists when running generate:tests.
     *
     * @param OutputInterface $output
     * @param bool $verbose
     * @return void
     */
    protected function removeGeneratedDirectory(OutputInterface $output, bool $verbose)
    {
        $generatedDirectory = TESTS_MODULE_PATH . DIRECTORY_SEPARATOR . TestGenerator::GENERATED_DIR;

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
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */

    protected function getTestAndSuiteConfiguration(array $tests)
    {
        $testConfiguration['tests'] = null;
        $testConfiguration['suites'] = null;
        $testsReferencedInSuites = SuiteObjectHandler::getInstance()->getAllTestReferences();
        $resolvedTests = [];

        foreach($tests as $test) {
            if (array_key_exists($test, $testsReferencedInSuites)) {
                $suites = $testsReferencedInSuites[$test];
                $resolvedTests = array_merge(
                    $resolvedTests,
                    array_map(function ($value) use ($test) {
                        return  $value . ':' . $test;
                    }, $suites)
                );
            }
            // configuration for tests
            else $testConfiguration['tests'][] = $test;
        }
        // configuration for suites
        foreach ($resolvedTests as $test) {
            list($suite, $test) = explode(":", $test);
            $testConfiguration['suites'][$suite][] = $test;
        }
        $testConfigurationJson = json_encode($testConfiguration);
        return $testConfigurationJson;
    }
}
