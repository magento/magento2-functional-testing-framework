<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;

/**
 * Class TestObjectExtractor
 */
class TestObjectExtractor extends BaseObjectExtractor
{
    const TEST_ANNOTATIONS = 'annotations';
    const TEST_BEFORE_HOOK = 'before';
    const TEST_AFTER_HOOK = 'after';
    const TEST_FAILED_HOOK = 'failed';
    const TEST_BEFORE_ATTRIBUTE = 'before';
    const TEST_AFTER_ATTRIBUTE = 'after';
    const TEST_INSERT_BEFORE = 'insertBefore';
    const TEST_INSERT_AFTER = 'insertAfter';
    const TEST_FILENAME = 'filename';

    /**
     * Action Object Extractor object
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * Annotation Extractor object
     *
     * @var AnnotationExtractor
     */
    private $annotationExtractor;

    /**
     * Test Hook Object extractor
     *
     * @var TestHookObjectExtractor
     */
    private $testHookObjectExtractor;

    /**
     * Module Path extractor
     *
     * @var ModulePathExtractor
     */
    private $modulePathExtractor;

    /**
     * TestObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
        $this->annotationExtractor = new AnnotationExtractor();
        $this->testHookObjectExtractor = new TestHookObjectExtractor();
        $this->modulePathExtractor = new ModulePathExtractor();
    }

    /**
     * Getter for AnnotationExtractor
     * @return AnnotationExtractor
     */
    public function getAnnotationExtractor()
    {
        return $this->annotationExtractor;
    }

    /**
     * This method takes and array of test data and strips away irrelevant tags. The data is converted into an array of
     * TestObjects.
     *
     * @param array $testData
     * @return TestObject
     * @throws \Exception
     */
    public function extractTestData($testData)
    {
        // validate the test name for blacklisted char (will cause allure report issues) MQE-483
        NameValidationUtil::validateName($testData[self::NAME], "Test");

        $testAnnotations = [];
        $testHooks = [];
        $filename = $testData['filename'] ?? null;
        $fileNames = explode(",", $filename);
        $baseFileName = $fileNames[0];
        $module = $this->modulePathExtractor->extractModuleName($baseFileName);
        $testReference = $testData['extends'] ?? null;
        $testActions = $this->stripDescriptorTags(
            $testData,
            self::NODE_NAME,
            self::NAME,
            self::TEST_ANNOTATIONS,
            self::TEST_BEFORE_HOOK,
            self::TEST_AFTER_HOOK,
            self::TEST_FAILED_HOOK,
            self::TEST_INSERT_BEFORE,
            self::TEST_INSERT_AFTER,
            self::TEST_FILENAME,
            'extends'
        );

        $testAnnotations = $this->annotationExtractor->extractAnnotations(
            $testData[self::TEST_ANNOTATIONS] ?? [],
            $testData[self::NAME]
        );

        //Override features with module name if present, populates it otherwise
        $testAnnotations["features"] = [$module];

        // Always try to append test file names in description annotation, i.e. displaying test files title only
        // when $fileNames is not available
        if (!isset($testAnnotations["description"])) {
            $testAnnotations["description"] = [];
        }
        $description = $testAnnotations["description"][0] ?? '';
        $testAnnotations["description"][0] = $this->appendFileNamesInDescriptionAnnotation(
            $description,
            $fileNames
        );

        // extract before
        if (array_key_exists(self::TEST_BEFORE_HOOK, $testData) && is_array($testData[self::TEST_BEFORE_HOOK])) {
            $testHooks[self::TEST_BEFORE_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'before',
                $testData[self::TEST_BEFORE_HOOK]
            );
        }

        if (array_key_exists(self::TEST_AFTER_HOOK, $testData) && is_array($testData[self::TEST_AFTER_HOOK])) {
            // extract after
            $testHooks[self::TEST_AFTER_HOOK] = $this->testHookObjectExtractor->extractHook(
                $testData[self::NAME],
                'after',
                $testData[self::TEST_AFTER_HOOK]
            );

            // extract failed
            $testHooks[self::TEST_FAILED_HOOK] = $this->testHookObjectExtractor->createDefaultFailedHook(
                $testData[self::NAME]
            );
        }

        // TODO extract filename info and store
        try {
            return new TestObject(
                $testData[self::NAME],
                $this->actionObjectExtractor->extractActions($testActions, $testData[self::NAME]),
                $testAnnotations,
                $testHooks,
                $filename,
                $testReference
            );
        } catch (XmlException $exception) {
            throw new XmlException($exception->getMessage() . ' in Test ' . $filename);
        }
    }

    /**
     * Append names of test files, including merge files, in description annotation
     *
     * @param string $description
     * @param array  $fileNames
     *
     * @return string
     */
    private function appendFileNamesInDescriptionAnnotation($description, $fileNames)
    {
        $description .= '<br><br><b><font size=+0.9>Test files</font></b><br><br>';

        foreach ($fileNames as $fileName) {
            if (!empty($fileName) && realpath($fileName) !== false) {
                $fileName = realpath($fileName);
                $relativeFileName = ltrim(
                    str_replace(MAGENTO_BP, '', $fileName),
                    DIRECTORY_SEPARATOR
                );
                if (!empty($relativeFileName)) {
                    $description .= $relativeFileName . '<br>';
                }
            }
        }

        return $description;
    }
}
