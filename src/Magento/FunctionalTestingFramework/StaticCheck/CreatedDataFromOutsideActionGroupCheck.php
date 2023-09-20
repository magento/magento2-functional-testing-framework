<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use InvalidArgumentException;
use Exception;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Symfony\Component\Finder\SplFileInfo;
use DOMNodeList;
use DOMElement;

/**
 * Class CreatedDataFromOutsideActionGroupCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CreatedDataFromOutsideActionGroupCheck implements StaticCheckInterface
{
    const ACTIONGROUP_REGEX_PATTERN = '/\$(\$)*([\w.]+)(\$)*\$/';
    const ERROR_LOG_FILENAME = 'create-data-from-outside-action-group';
    const ERROR_MESSAGE = 'Created Data From Outside Action Group';

    /**
     * Array containing all errors found after running the execute() function
     *
     * @var array
     */
      private $errors = [];

    /**
     * String representing the output summary found after running the execute() function
     *
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
     * @var array
     */
    private $actionGroupXmlFile = [];

    /**
     * Checks test dependencies, determined by references in tests versus the dependencies listed in the Magento module
     *
     * @param InputInterface $input
     * @return void
     * @throws Exception
     */
    public function execute(InputInterface $input)
    {
        $this->scriptUtil = new ScriptUtil();
        $this->loadAllXmlFiles($input);
        $this->errors = [];
        $this->errors += $this->findReferenceErrorsInActionFiles($this->actionGroupXmlFile);
        // hold on to the output and print any errors to a file
        $this->output = $this->scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_MESSAGE
        );
    }

    /**
     * Return array containing all errors found after running the execute() function
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No Dependency errors found."
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Read all XML files for scanning
     *
     * @param InputInterface $input
     * @return void
     * @throws Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function loadAllXmlFiles($input)
    {
        $modulePaths = [];
        $path = $input->getOption('path');
        if ($path) {
            if (!realpath($path)) {
                throw new InvalidArgumentException('Invalid --path option: ' . $path);
            }
            MftfApplicationConfig::create(
                true,
                MftfApplicationConfig::UNIT_TEST_PHASE,
                false,
                MftfApplicationConfig::LEVEL_DEFAULT,
                true
            );
            $modulePaths[] = realpath($path);
        } else {
            $modulePaths = $this->scriptUtil->getAllModulePaths();
        }

        // These files can contain references to other entities
        $this->actionGroupXmlFile = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'ActionGroup');
      
        if (empty($this->actionGroupXmlFile)) {
            if ($path) {
                throw new InvalidArgumentException(
                    'Invalid --path option: '
                    . $path
                    . PHP_EOL
                    . 'Please make sure --path points to a valid MFTF Test Module.'
                );
            } elseif (empty($this->rootSuiteXmlFiles)) {
                throw new TestFrameworkException('No xml file to scan.');
            }
        }
    }

    /**
     * Find reference errors in set of action files
     *
     * @param Finder $files
     * @return array
     * @throws XmlException
     */
    private function findReferenceErrorsInActionFiles($files)
    {
        $testErrors = [];
        /** @var SplFileInfo $filePath */
        foreach ($files as $filePath) {
            $contents = file_get_contents($filePath);
            preg_match_all(self::ACTIONGROUP_REGEX_PATTERN, $contents, $actionGroupReferences);
            if (count($actionGroupReferences) > 0) {
                $testErrors = array_merge($testErrors, $this->setErrorOutput($actionGroupReferences, $filePath));
            }
        }

        return $testErrors;
    }

     /**
      * Build and return error output for violating references
      *
      * @param array       $actionGroupReferences
      * @param SplFileInfo $path
      * @return mixed
      */
    private function setErrorOutput($actionGroupReferences, $path)
    {
        $testErrors = [];
        $errorOutput = "";
        $filePath = StaticChecksList::getFilePath($path->getRealPath());
       
        foreach ($actionGroupReferences as $key => $actionGroupReferencesData) {
            foreach ($actionGroupReferencesData as $actionGroupReferencesDataResult) {
                $errorOutput .= "\nFile \"{$filePath}\" contains: ". "\n\t 
                {$actionGroupReferencesDataResult}  in {$filePath}";
                $testErrors[$filePath][] = $errorOutput;
            }
        }
        return $testErrors;
    }
}
