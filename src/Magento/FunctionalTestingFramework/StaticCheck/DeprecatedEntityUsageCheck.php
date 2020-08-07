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
 * Class DeprecatedEntityUsageCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeprecatedEntityUsageCheck implements StaticCheckInterface
{
    const EXTENDS_REGEX_PATTERN = '/extends=["\']([^\'"]*)/';
    const ACTIONGROUP_REGEX_PATTERN = '/ref=["\']([^\'"]*)/';
    const DEPRECATED_REGEX_PATTERN = '/deprecated=["\']([^\'"]*)/';

    const ERROR_LOG_FILENAME = 'mftf-deprecated-entity-usage-checks';
    const ERROR_LOG_MESSAGE = 'MFTF Deprecated Entity Usage Check';

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
     * Data operations
     *
     * @var array
     */
    private $dataOperations = ['create', 'update', 'get', 'delete'];

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
     * Data xml files to scan
     *
     * @var Finder|array
     */
    private $dataXmlFiles = [];

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
        $this->errors += $this->findReferenceErrorsInActionFiles($this->testXmlFiles);
        $this->errors += $this->findReferenceErrorsInActionFiles($this->actionGroupXmlFiles);
        $this->errors += $this->findReferenceErrorsInActionFiles($this->suiteXmlFiles, true);
        if (!empty($this->rootSuiteXmlFiles)) {
            $this->errors += $this->findReferenceErrorsInActionFiles($this->rootSuiteXmlFiles, true);
        }
        $this->errors += $this->findReferenceErrorsInDataFiles($this->dataXmlFiles);

        // Hold on to the output and print any errors to a file
        $this->output = $this->scriptUtil->printErrorsToFile(
            $this->errors,
            StaticChecksList::getErrorFilesPath() . DIRECTORY_SEPARATOR . self::ERROR_LOG_FILENAME . '.txt',
            self::ERROR_LOG_MESSAGE
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
        $includeRootPath = true;
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
            $includeRootPath = false;
        } else {
            $modulePaths = $this->scriptUtil->getAllModulePaths();
        }

        // These files can contain references to other entities
        $this->testXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'Test');
        $this->actionGroupXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'ActionGroup');
        $this->suiteXmlFiles = $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'Suite');
        if ($includeRootPath) {
            $this->rootSuiteXmlFiles = $this->scriptUtil->getRootSuiteXmlFiles();
        }
        $this->dataXmlFiles= $this->scriptUtil->getModuleXmlFilesByScope($modulePaths, 'Data');

        if (empty($this->thistestXmlFiles)
            && empty($this->actionGroupXmlFiles)
            && empty($this->suiteXmlFiles)
            && empty($this->dataXmlFiles)
        ) {
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
     * @param Finder  $files
     * @param boolean $checkTestRef
     * @return array
     * @throws XmlException
     */
    private function findReferenceErrorsInActionFiles($files, $checkTestRef = false)
    {
        $testErrors = [];
        /** @var SplFileInfo $filePath */
        foreach ($files as $filePath) {
            $contents = file_get_contents($filePath);
            if ($this->isDeprecated($contents)) {
                continue;
            }
            preg_match_all(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN, $contents, $braceReferences);
            preg_match_all(self::ACTIONGROUP_REGEX_PATTERN, $contents, $actionGroupReferences);
            preg_match_all(self::EXTENDS_REGEX_PATTERN, $contents, $extendReferences);

            $domDocument = new \DOMDocument();
            $domDocument->load($filePath);
            $createdDataReferences = $this->getAttributesFromDOMNodeList(
                $domDocument->getElementsByTagName('createData'),
                'entity'
            );
            $updatedDataReferences = $this->getAttributesFromDOMNodeList(
                $domDocument->getElementsByTagName('updateData'),
                'entity'
            );
            $getDataReferences = $this->getAttributesFromDOMNodeList(
                $domDocument->getElementsByTagName('getData'),
                'entity'
            );

            // Remove Duplicates
            $actionGroupReferences[1] = array_unique($actionGroupReferences[1]);
            $extendReferences[1] = array_unique($extendReferences[1]);
            $braceReferences[0] = array_unique($braceReferences[0]);
            $braceReferences[2] = array_filter(array_unique($braceReferences[2]));
            $createdDataReferences = array_unique($createdDataReferences);
            $updatedDataReferences = array_unique($updatedDataReferences);
            $getDataReferences = array_unique($getDataReferences);

            // Resolve entity references
            $entityReferences = $this->scriptUtil->resolveEntityReferences($braceReferences[0], $contents, true);
            // Resolve parameterized references
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveParametrizedReferences($braceReferences[2], $contents, true)
            );
            // Resolve action group entity by names
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveEntityByNames($actionGroupReferences[1])
            );
            // Resolve extends entity by names
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveEntityByNames($extendReferences[1])
            );
            // Resolve create data entity by names
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveEntityByNames($createdDataReferences)
            );
            // Resolve update data entity by names
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveEntityByNames($updatedDataReferences)
            );
            // Resolve get data entity by names
            $entityReferences = array_merge(
                $entityReferences,
                $this->scriptUtil->resolveEntityByNames($getDataReferences)
            );
            // Find test references if needed
            if ($checkTestRef) {
                $entityReferences = array_merge($entityReferences, $this->resolveTestEntityInSuite($domDocument));
            }

            // Find violating references
            $violatingReferences = $this->findViolatingReferences($entityReferences);

            // Find metadata references from persist data
            $metadataReferences = $this->getMetadataFromData($createdDataReferences, 'create');
            $metadataReferences = array_merge_recursive(
                $metadataReferences,
                $this->getMetadataFromData($updatedDataReferences, 'update')
            );
            $metadataReferences = array_merge_recursive(
                $metadataReferences,
                $this->getMetadataFromData($getDataReferences, 'get')
            );

            // Find violating references
            $violatingReferences = array_merge(
                $violatingReferences,
                $this->findViolatingMetadataReferences($metadataReferences)
            );

            // Set error output
            $testErrors = array_merge($testErrors, $this->setErrorOutput($violatingReferences, $filePath));
        }
        return $testErrors;
    }

    /**
     * Checks if entity is deprecated in action files.
     * @param string $contents
     * @return boolean
     */
    private function isDeprecated($contents)
    {
        preg_match_all(self::DEPRECATED_REGEX_PATTERN, $contents, $deprecatedEntity);
        return (!empty($deprecatedEntity[1]));
    }

    /**
     * Find reference errors in a set of data files
     *
     * @param Finder $files
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function findReferenceErrorsInDataFiles($files)
    {
        $testErrors = [];
        /** @var SplFileInfo $filePath */
        foreach ($files as $filePath) {
            $dataReferences = [];
            $metadataReferences = [];
            $domDocument = new \DOMDocument();
            $domDocument->load($filePath);
            $entities = $domDocument->getElementsByTagName('entity');
            foreach ($entities as $entity) {
                /** @var DOMElement $entity */
                $deprecated = $entity->getAttribute('deprecated');
                // skip check if entity is deprecated
                if (!empty($deprecated)) {
                    continue;
                }
                $entityName = $entity->getAttribute('name');
                $metadataType = $entity->getAttribute('type');
                $parentEntityName = $entity->getAttribute('extends');
                if (!empty($metadataType && !isset($metadataReferences[$entityName][$metadataType]))) {
                    $metadataReferences[$entityName][$metadataType] = 'all';
                }
                if (!empty($parentEntityName)) {
                    $dataReferences[$entityName][] = $parentEntityName;
                }
                // Find metadata reference in `var` elements
                $varElements = $entity->getElementsByTagName('var');
                foreach ($varElements as $varElement) {
                    /** @var DOMElement $varElement */
                    $metadataType = $varElement->getAttribute('entityType');
                    if (!empty($metadataType) && !isset($metadataReferences[$entityName][$metadataType])) {
                        $metadataReferences[$entityName][$metadataType] = 'all';
                    }
                }
                // Find metadata reference in `requiredEntity` elements, and
                // Find data references in `requiredEntity` elements
                $requiredElements = $entity->getElementsByTagName('requiredEntity');
                foreach ($requiredElements as $requiredElement) {
                    /** @var DOMElement $requiredElement */
                    $metadataType = $requiredElement->getAttribute('type');
                    if (!empty($metadataType) && !isset($metadataReferences[$entityName][$metadataType])) {
                        $metadataReferences[$entityName][$metadataType] = 'all';
                    }
                    $dataReferences[$entityName][] = $requiredElement->nodeValue;
                }
            }

            // Find violating references
            // Metadata references is unique
            $violatingReferences = $this->findViolatingMetadataReferences($metadataReferences);
            // Data references is not unique
            $violatingReferences = array_merge_recursive(
                $violatingReferences,
                $this->findViolatingDataReferences($this->twoDimensionArrayUnique($dataReferences))
            );

            // Set error output
            $testErrors = array_merge($testErrors, $this->setErrorOutput($violatingReferences, $filePath));
        }
        return $testErrors;
    }

    /**
     * Trim duplicate values from two-dimensional array. Dimension 1 array key is unique.
     *
     * @param array $inArray
     * @return array
     */
    private function twoDimensionArrayUnique($inArray)
    {
        $outArray = [];
        foreach ($inArray as $key => $arr) {
            $outArray[$key] = array_unique($arr);
        }
        return $outArray;
    }

    /**
     * Return attribute value for each node in DOMNodeList as an array
     *
     * @param DOMNodeList $nodes
     * @param string      $attributeName
     * @return array
     */
    private function getAttributesFromDOMNodeList($nodes, $attributeName)
    {
        $attributes = [];
        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $attributeValue = $node->getAttribute($attributeName);
            if (!empty($attributeValue)) {
                $attributes[] = $attributeValue;
            }
        }
        return $attributes;
    }

    /**
     * Find metadata from data array
     *
     * @param array  $references
     * @param string $type
     * @return array
     */
    private function getMetadataFromData($references, $type)
    {
        $metaDataReferences = [];
        try {
            foreach ($references as $dataName) {
                /** @var EntityDataObject $dataEntity */
                $dataEntity = $this->scriptUtil->findEntity($dataName);
                if ($dataEntity) {
                    $metadata = $dataEntity->getType();
                    if (!empty($metadata)) {
                        $metaDataReferences[$dataName][$metadata] = $type;
                    }
                }
            }
        } catch (Exception $e) {
        }
        return $metaDataReferences;
    }

    /**
     * Find violating metadata references. Input array format is either
     *
     *  [
     *      $dataName1 => [
     *          $metaDataName1 = [
     *              'create',
     *              'update',
     *          ],
     *      ],
     *      $dataName2 => [
     *          $metaDataName2 => 'create',
     *      ],
     *      $dataName3 => [
     *          $metaDataName3 = [
     *              'get',
     *              'create',
     *              'update',
     *          ],
     *      ],
     *      ...
     *  ]
     *
     *  or
     *
     *  [
     *      $dataName1 => [
     *          $metaDataName1 => 'all',
     *          $metaDataName2 => 'all',
     *          ...
     *      ],
     *      $dataName2 => [
     *          $metaDataName2 => 'all',
     *          ...
     *      ],
     *      $dataName5 => [
     *          $metaDataName5 => 'all',
     *          $metaDataName4 => 'all',
     *          $metaDataName1 => 'all',
     *          ...
     *      ],
     *      ...
     *  ]
     *
     * @param array $references
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function findViolatingMetadataReferences($references)
    {
        $allObjects = OperationDefinitionObjectHandler::getInstance()->getAllObjects();

        // Find Violations
        $violatingReferences = [];
        foreach ($references as $dataName => $metadataArray) {
            foreach ($metadataArray as $metadata => $types) {
                $operations = [];
                $strict = true;
                if (is_array($types)) {
                    $operations = $types;
                } elseif ($types === 'all') {
                    $operations = $this->dataOperations;
                    $strict = false;
                } else {
                    $operations = [$types];
                }

                $deprecated = null;
                $exists = false;
                foreach ($operations as $operation) {
                    if (array_key_exists($operation . $metadata, $allObjects)) {
                        $exists = true;
                        /** @var OperationDefinitionObject $entity */
                        $entity = $allObjects[$operation . $metadata];
                        // When not strictly checking, it's not deprecated as long as we found one that's not deprecated
                        if (!$strict && empty($entity->getDeprecated())) {
                            $deprecated = false;
                            break;
                        }
                        // When strictly checking, it's deprecated as long as we found one that's deprecated
                        if ($strict && !empty($entity->getDeprecated())) {
                            $deprecated = true;
                            break;
                        }
                    }
                }
                if ($exists && !$strict && $deprecated !== false) {
                    $deprecated = true;
                }
                if ($strict && $deprecated !== true) {
                    $deprecated = false;
                }

                if ($deprecated) {
                    $violatingReferences["\"{$dataName}\" references deprecated"][] = [
                        'name' => $metadata,
                        // TODO add filename in OperationDefinitionObject
                        'file' => 'metadata xml file',
                    ];
                }
            }
        }
        return $violatingReferences;
    }

    /**
     * Find violating data references. Input array format is
     *
     *  [
     *      $dataName1 => [ $requiredDataName1, $requiredDataName2, $requiredDataName3],
     *      $dataName2 => [ $requiredDataName2, $requiredDataName5, $requiredDataName7],
     *      ...
     *  ]
     *
     * @param array $references
     * @return array
     */
    private function findViolatingDataReferences($references)
    {
        // Find Violations
        $violatingReferences = [];
        foreach ($references as $dataName => $requiredDataNames) {
            foreach ($requiredDataNames as $requiredDataName) {
                try {
                    /** @var EntityDataObject $requiredData */
                    $requiredData = DataObjectHandler::getInstance()->getObject($requiredDataName);
                    if ($requiredData && $requiredData->getDeprecated()) {
                        $violatingReferences["\"{$dataName}\" references deprecated"][] = [
                            'name' => $requiredDataName,
                            'file' => $requiredData->getFilename(),
                        ];
                    }
                } catch (Exception $e) {
                }
            }
        }
        return $violatingReferences;
    }

    /**
     * Find violating references. Input array format is
     *
     *  [
     *      'actionGroupName' => $actionGroupEntity,
     *      'dataGroupName' => $dataEntity,
     *      'testName' => $testEntity,
     *      'pageName' => $pageEntity,
     *      'section.field' => $fieldElementEntity,
     *      ...
     *  ]
     *
     * @param array $references
     * @return array
     */
    private function findViolatingReferences($references)
    {
        // Find Violations
        $violatingReferences = [];
        foreach ($references as $key => $entity) {
            if ($entity->getDeprecated()) {
                $classType = get_class($entity);
                $name = $entity->getName();
                if ($classType === ElementObject::class) {
                    $name = $key;
                    list($section,) = explode('.', $key, 2);
                    /** @var SectionObject $references[$section] */
                    $file = StaticChecksList::getFilePath($references[$section]->getFilename());
                } else {
                    $file = StaticChecksList::getFilePath($entity->getFilename());
                }
                $violatingReferences[$this->getSubjectFromClassType($classType)][] = [
                    'name' => $name,
                    'file' => $file,
                ];
            }
        }
        return $violatingReferences;
    }

    /**
     * Build and return error output for violating references
     *
     * @param array       $violatingReferences
     * @param SplFileInfo $path
     * @return mixed
     */
    private function setErrorOutput($violatingReferences, $path)
    {
        $testErrors = [];

        $filePath = StaticChecksList::getFilePath($path->getRealPath());

        if (!empty($violatingReferences)) {
            // Build error output
            $errorOutput = "\nFile \"{$filePath}\" contains:\n";
            foreach ($violatingReferences as $subject => $data) {
                $errorOutput .= "\t- {$subject}:\n";
                foreach ($data as $item) {
                    $errorOutput .= "\t\t\"" . $item['name'] . "\" in " . $item['file'] . "\n";
                }
            }
            $testErrors[$filePath][] = $errorOutput;
        }

        return $testErrors;
    }

    /**
     * Resolve test entity in suite
     *
     * @param \DOMDocument $domDocument
     * @return array
     */
    private function resolveTestEntityInSuite($domDocument)
    {
        $testReferences = $this->getAttributesFromDOMNodeList(
            $domDocument->getElementsByTagName('test'),
            'name'
        );

        // Remove Duplicates
        $testReferences = array_unique($testReferences);

        // Resolve test entity by names
        try {
            return $this->scriptUtil->resolveEntityByNames($testReferences);
        } catch (XmlException $e) {
            return [];
        }
    }

    /**
     * Return subject string for a class name
     *
     * @param string $classname
     * @return string|null
     */
    private function getSubjectFromClassType($classname)
    {
        $subject = null;
        if ($classname === ActionGroupObject::class) {
            $subject = 'Deprecated ActionGroup(s)';
        } elseif ($classname === TestObject::class) {
            $subject = 'Deprecated Test(s)';
        } elseif ($classname === SectionObject::class) {
            $subject = 'Deprecated Section(s)';
        } elseif ($classname === PageObject::class) {
            $subject = 'Deprecated Page(s)';
        } elseif ($classname === ElementObject::class) {
            $subject = 'Deprecated Element(s)';
        } elseif ($classname === EntityDataObject::class) {
            $subject = 'Deprecated Data(s)';
        } elseif ($classname === OperationDefinitionObject::class) {
            $subject = 'Deprecated Metadata(s)';
        }
        return $subject;
    }
}
