<?php

namespace Magento\AcceptanceTestFramework\Test\Handlers;

use Magento\AcceptanceTestFramework\Exceptions\XmlException;
use Magento\AcceptanceTestFramework\ObjectManager\ObjectHandlerInterface;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\Test\Objects\ActionObject;
use Magento\AcceptanceTestFramework\Test\Objects\CestObject;
use Magento\AcceptanceTestFramework\Test\Objects\TestObject;
use Magento\AcceptanceTestFramework\Test\Objects\CestHookObject;
use Magento\AcceptanceTestFramework\Test\TestDataParser;

class CestObjectHandler implements ObjectHandlerInterface
{
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";
    const CEST_ROOT = 'config';
    const CEST_ANNOTATIONS = 'annotations';
    const CEST_BEFORE_HOOK = 'before';
    const CEST_AFTER_HOOK = 'after';
    const CEST_TEST_TAG = 'test';
    const TEST_ANNOTATIONS = 'annotations';
    const TEST_ACTION_BEFORE = 'before';
    const TEST_ACTION_AFTER = 'after';
    const TEST_STEP_MERGE_KEY = 'mergeKey';
    const NODE_NAME = 'nodeName';
    const NAME = 'name';
    const ANNOTATION_VALUE = 'value';

    /**
     * @var CestObjectHandler $cestObjectHandler
     */
    private static $cestObjectHandler;

    /**
     * Array contains all cest objects indexed by name
     * @var array $cests
     */
    private $cests = [];

    /**
     * Singleton method to return CestObjectHandler.
     * @return CestObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$cestObjectHandler) {
            $testDataParser = ObjectManagerFactory::getObjectManager()->create(TestDataParser::class);
            $parsedCestArray = $testDataParser->readTestData();
            self::$cestObjectHandler = new CestObjectHandler();
            self::$cestObjectHandler->initCestData($parsedCestArray);
        }

        return self::$cestObjectHandler;
    }

    /**
     * CestObjectHandler constructor.
     * @constructor
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Takes a cest name and returns the corresponding cest.
     * @param string $cestName
     * @return CestObject
     */
    public function getObject($cestName)
    {
        return $this->cests[$cestName];
    }

    /**
     * Returns all cests parsed from xml indexed by cestName.
     * @return array
     */
    public function getAllObjects()
    {
        return $this->cests;
    }

    /**
     * This method takes the parsed cest array and returns CestObjects parsed from defined *Cest.xml files
     * @param array $parsedArray
     * @return array
     */
    private function initCestData($parsedArray)
    {
        foreach ($parsedArray[CestObjectHandler::CEST_ROOT] as $cestName => $cestData) {
            $hooks = [];
            $annotations = [];

            if (!is_array($cestData)) {
                continue;
            }

            $tests = $this->stripDescriptorTags(
                $cestData,
                CestObjectHandler::NODE_NAME,
                CestObjectHandler::NAME
            );

            if (array_key_exists(CestObjectHandler::CEST_BEFORE_HOOK, $cestData)) {
                $hooks[CestObjectHandler::CEST_BEFORE_HOOK] = $this->extractHook(
                    CestObjectHandler::CEST_BEFORE_HOOK,
                    $cestData[CestObjectHandler::CEST_BEFORE_HOOK]
                );

                $tests = $this->stripDescriptorTags($tests, CestObjectHandler::CEST_BEFORE_HOOK);
            }

            if (array_key_exists(CestObjectHandler::CEST_AFTER_HOOK, $cestData)) {
                $hooks[CestObjectHandler::CEST_AFTER_HOOK] = $this->extractHook(
                    CestObjectHandler::CEST_AFTER_HOOK,
                    $cestData[CestObjectHandler::CEST_AFTER_HOOK]
                );

                $tests = $this->stripDescriptorTags($tests, CestObjectHandler::CEST_AFTER_HOOK);
            }

            if (array_key_exists(CestObjectHandler::CEST_ANNOTATIONS, $cestData)) {
                $annotations = $this->extractAnnotations($cestData[CestObjectHandler::CEST_ANNOTATIONS]);

                $tests = $this->stripDescriptorTags($tests, CestObjectHandler::CEST_ANNOTATIONS);
            }

            $this->cests[$cestName] = new CestObject(
                $cestName,
                $annotations,
                $this->extractTestData($tests),
                $hooks
            );
        }
    }

    /**
     * This method trims all irrelevant tags to extract hook information including before and after tags
     * and their relevant actions. The result is an array of CestHookObjects.
     *
     * @param string $hookType
     * @param array $cestHook
     * @return CestHookObject
     */
    private function extractHook($hookType, $cestHook)
    {
        $hookActions = $this->stripDescriptorTags(
            $cestHook,
            CestObjectHandler::NODE_NAME
        );

        $hook = new CestHookObject(
            $hookType,
            $this->extractTestActions($hookActions)
        );


        return $hook;
    }

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Cests and their child element tests.
     *
     * @param array $cestAnnotations
     * @return array
     */
    private function extractAnnotations($cestAnnotations)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($cestAnnotations, CestObjectHandler::NODE_NAME);

        // parse the Cest annotations
        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationValues = [];
            foreach ($annotationData as $annotationValue) {
                $annotationValues[] = $annotationValue[CestObjectHandler::ANNOTATION_VALUE];
            }

            $annotationObjects[$annotationKey] = $annotationValues;
        }

        return $annotationObjects;
    }

    /**
     * This method takes and array of test data and strips away irrelevant tags. The data is converted into an array of
     * TestObjects.
     *
     * @param array $cestTestData
     * @return array
     */
    private function extractTestData($cestTestData)
    {
        $testObjects = [];

        // parse the tests
        foreach ($cestTestData as $testName => $testData) {
            if (!is_array($testData)) {
                continue;
            }

            $testAnnotations = [];
            $testActions = $this->stripDescriptorTags(
                $testData,
                CestObjectHandler::NODE_NAME,
                CestObjectHandler::NAME,
                CestObjectHandler::TEST_ANNOTATIONS
            );

            if (array_key_exists(CestObjectHandler::TEST_ANNOTATIONS, $testData)) {
                $testAnnotations = $this->extractAnnotations($testData[CestObjectHandler::TEST_ANNOTATIONS]);
            }

            $testObjects[] = new TestObject(
                $testName,
                $this->extractTestActions($testActions),
                $testAnnotations
            );
        }

        return $testObjects;
    }

    /**
     * This method takes an array of test actions read in from a CestHook or Test. The actions are stripped of
     * irrelevant tags and returned as an array of ActionObjects.
     *
     * @param array $testActions
     * @return array
     * @throws XmlException
     */
    private function extractTestActions($testActions)
    {
        $actions = [];

        foreach ($testActions as $actionName => $actionData) {
            $mergeKey = $actionData[CestObjectHandler::TEST_STEP_MERGE_KEY];
            $actionAttributes = $this->stripDescriptorTags(
                $actionData,
                CestObjectHandler::TEST_STEP_MERGE_KEY,
                CestObjectHandler::NODE_NAME
            );
            $linkedAction = null;
            $order = null;

            if (array_key_exists(CestObjectHandler::TEST_ACTION_BEFORE, $actionData)
                and array_key_exists(CestObjectHandler::TEST_ACTION_AFTER, $actionData)) {
                throw new XmlException(sprintf(self::BEFORE_AFTER_ERROR_MSG, $actionName));
            }

            if (array_key_exists(CestObjectHandler::TEST_ACTION_BEFORE, $actionData)) {
                $linkedAction = $actionData[CestObjectHandler::TEST_ACTION_BEFORE];
                $order = "before";
            } elseif (array_key_exists(CestObjectHandler::TEST_ACTION_AFTER, $actionData)) {
                $linkedAction = $actionData[CestObjectHandler::TEST_ACTION_AFTER];
                $order = "after";
            }
            // TODO this is to be implemented later. Currently the schema does not use or need return var.
            /*if (array_key_exists(CestObjectHandler::TEST_ACTION_RETURN_VARIABLE, $actionData)) {
                $returnVariable = $actionData[CestObjectHandler::TEST_ACTION_RETURN_VARIABLE];
            }*/

            $actions[] = new ActionObject(
                $mergeKey,
                $actionData[CestObjectHandler::NODE_NAME],
                $actionAttributes,
                $linkedAction,
                $order
            );
        }

        return $actions;
    }


    /**
     * This method takes an array of data and an array representing irrelevant tags. The method strips
     * the data passed in of the irrelevant tags and returns the result.
     *
     * @param array $data
     * @param array $tags
     * @return array
     */
    private function stripDescriptorTags($data, ...$tags)
    {
        $results = $data;
        foreach ($tags as $tag) {
            unset($results[$tag]);
        }

        return $results;
    }
}
