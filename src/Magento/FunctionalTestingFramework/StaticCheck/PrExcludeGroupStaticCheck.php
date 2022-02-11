<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Exception;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class PrExcludeGroupStaticCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class PrExcludeGroupStaticCheck implements StaticCheckInterface
{
    const GROUP_NAME = 'pr_exclude';
    const ERROR_LOG_FILENAME = 'mftf-pr-exclude-usage-checks';
    const ERROR_LOG_MESSAGE = 'MFTF pr_exclude Group Usage Check';

    /**
     * Array containing all errors found after running the execute() function.
     * @var array
     */
    private $errors = [];

    /**
     * String representing the output summary found after running the execute() function.
     * @var string
     */
    private $output;

    /**
     * ScriptUtil instance
     *
     * @var ScriptUtil
     */
    private $scriptUtil;

    /**
     * Test xml files to scan
     *
     * @var Finder|array
     */
    private $testXmlFiles = [];

    /**
     * Action group xml files to scan
     *
     * @var Finder|array
     */
    private $actionGroupXmlFiles = [];

    /**
     * Suite xml files to scan
     *
     * @var Finder|array
     */
    private $suiteXmlFiles = [];

    /**
     * Root suite xml files to scan
     *
     * @var Finder|array
     */
    private $rootSuiteXmlFiles = [];

    /**
     * Checks usage of pause action in action groups, tests and suites and prints out error to file.
     *
     * @param InputInterface $input
     * @return void
     * @throws Exception
     */
    public function execute(InputInterface $input)
    {
        $this->scriptUtil = new ScriptUtil();
        $modulePaths = [];
        $path = $input->getOption('path');
        if ($path) {
            if (!realpath($path)) {
                throw new \InvalidArgumentException('Invalid --path option: ' . $path);
            }
            $modulePaths[] = realpath($path);
        } else {
            $modulePaths = $this->scriptUtil->getAllModulePaths();
        }

        $this->testXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'Test');
        $this->errors = [];
        $this->errors += $this->validatePrExcludeGroupUsageInTests($this->testXmlFiles);

        $this->output = $this->scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_LOG_MESSAGE
        );
    }

    /**
     * Finds usages of pr_exclude group in test files
     * @param array $testXmlFiles
     * @return array
     */
    private function validatePrExcludeGroupUsageInTests($testXmlFiles)
    {
        $testErrors = [];
        foreach ($testXmlFiles as $filePath) {
            $domDocument = new \DOMDocument();
            $domDocument->load($filePath);
            $test = $domDocument->getElementsByTagName('test')->item(0);
            if ($this->isViolatingPrExcludeTests($test)) {
                $testErrors = array_merge($testErrors, $this->setErrorOutput($filePath));
            }
        }
        return $testErrors;
    }

    /**
     * Finds violating pr_exclude group
     * @param \DomNode $entity
     * @return bool
     */
    private function isViolatingPrExcludeTests($entity)
    {
        $violation = false;
        $references = $entity->getElementsByTagName('group');

        foreach ($references as $reference) {
            $groupValue = $reference->getAttribute('value');
            if ($groupValue === self::GROUP_NAME) {
                $violation = true;
                break;
            }
        }

        return $violation;
    }

    /**
     * Return array containing all errors found after running the execute() function.
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No errors found."
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Build and return error output
     *
     * @param SplFileInfo $path
     * @return mixed
     */
    private function setErrorOutput($path)
    {
        $testErrors = [];

        $filePath = $this->getFilePath($path->getRealPath());

        // Build error output
        $errorOutput = "\nFile \"{$filePath}\"";
        $errorOutput .= "\ncontains group 'pr_exclude' which is not allowed.\n";
        $testErrors[$filePath][] = $errorOutput;

        return $testErrors;
    }

    /**
     * Return relative path to files.
     * @param string $fileNames
     * @return string
     */
    private function getFilePath($fileNames)
    {
        if (!empty($fileNames)) {
            $relativeFileNames = ltrim(
                str_replace(MAGENTO_BP, '', $fileNames)
            );
            if (!empty($relativeFileNames)) {
                return $relativeFileNames;
            }
        }
        return $fileNames;
    }
}
