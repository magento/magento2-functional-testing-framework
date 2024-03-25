<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Symfony\Component\Console\Input\InputInterface;
use Exception;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ClassFileNamingCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class ClassFileNamingCheck implements StaticCheckInterface
{
    const ERROR_LOG_FILENAME = 'mftf-class-file-naming-check';
    const ERROR_LOG_MESSAGE = 'MFTF Class File Naming Check';
    const ALLOW_LIST_FILENAME = 'class-file-naming-allowlist';
    const WARNING_LOG_FILENAME = 'mftf-class-file-naming-warnings';

    /**
     * Array containing all warnings found after running the execute() function.
     * @var array
     */
    private $warnings = [];
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
     * @var array $allowFailureEntities
     */
    private $allowFailureEntities = [];

    /**
     * ScriptUtil instance
     *
     * @var ScriptUtil
     */
    private $scriptUtil;
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
        foreach ($modulePaths as $modulePath) {
            if (file_exists($modulePath . DIRECTORY_SEPARATOR . self::ALLOW_LIST_FILENAME)) {
                $contents = file_get_contents($modulePath . DIRECTORY_SEPARATOR . self::ALLOW_LIST_FILENAME);
                foreach (explode("\n", $contents) as $entity) {
                    $this->allowFailureEntities[$entity] = true;
                }
            }
        }
        $testXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, "Test");
        $actionGroupXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, "ActionGroup");
        $pageXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, "Page");
        $sectionXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, "Section");
        $suiteXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'Suite');
        $this->errors = [];
        $this->errors += $this->findErrorsInFileSet($testXmlFiles, 'test');
        $this->errors += $this->findErrorsInFileSet($actionGroupXmlFiles, 'actionGroup');
        $this->errors += $this->findErrorsInFileSet($pageXmlFiles, 'page');
        $this->errors += $this->findErrorsInFileSet($sectionXmlFiles, 'section');
        $this->errors += $this->findErrorsInFileSet($suiteXmlFiles, 'suite');

        // hold on to the output and print any errors to a file
        $this->output = $this->scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_LOG_MESSAGE
        );
        if (!empty($this->warnings)) {
            $this->output .= "\n " . $this->scriptUtil->printWarningsToFile(
                $this->warnings,
                StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::WARNING_LOG_FILENAME . '.txt',
                self::ERROR_LOG_MESSAGE
            );
        }
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
     * Returns Violations if found
     * @param  SplFileInfo $files
     * @param  string      $fileType
     * @return array
     */
    public function findErrorsInFileSet($files, $fileType)
    {
        $errors = [];
        /** @var SplFileInfo $filePath */

        foreach ($files as $filePath) {
            $fileNameWithoutExtension = pathinfo($filePath->getFilename(), PATHINFO_FILENAME);
            $domDocument = new \DOMDocument();
            $domDocument->load($filePath);
            $testResult = $this->getAttributesFromDOMNodeList(
                $domDocument->getElementsByTagName($fileType),
                ["type" => 'name']
            );
            if ($fileNameWithoutExtension != array_values($testResult[0])[0]) {
                $isInAllowList = array_key_exists(array_values($testResult[0])[0], $this->allowFailureEntities);
                if ($isInAllowList) {
                     $errorOutput = ucfirst($fileType). " name does not match with file name 
                    {$filePath->getRealPath()}. ".ucfirst($fileType)." ".array_values($testResult[0])[0];
                     $this->warnings[$filePath->getFilename()][] = $errorOutput;
                     continue;
                }
                $errorOutput =  ucfirst($fileType). " name does not match with file name 
                    {$filePath->getRealPath()}. ".ucfirst($fileType)." ".array_values($testResult[0])[0];
                $errors[$filePath->getFilename()][] = $errorOutput;
            }
        }
        return $errors;
    }

    /**
     * Return attribute value for each node in DOMNodeList as an array
     *
     * @param  DOMNodeList $nodes
     * @param  string      $attributeName
     * @return array
     */
    public function getAttributesFromDOMNodeList($nodes, $attributeName)
    {
        $attributes = [];
        foreach ($nodes as $node) {
            if (is_string($attributeName)) {
                $attributeValue = $node->getAttribute($attributeName);
            } else {
                $attributeValue = [$node->getAttribute(key($attributeName)) =>
                    $node->getAttribute($attributeName[key($attributeName)])];
            }
            if (!empty($attributeValue)) {
                $attributes[] = $attributeValue;
            }
        }
        return $attributes;
    }
}
