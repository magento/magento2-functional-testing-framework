<?php

namespace Magento\AcceptanceTestFramework\Test\Managers;

use Magento\AcceptanceTestFramework\Exceptions\XmlException;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\Test\CestDataConstants;
use Magento\AcceptanceTestFramework\Test\Objects\ActionObject;
use Magento\AcceptanceTestFramework\Test\Objects\CestObject;
use Magento\AcceptanceTestFramework\Test\Objects\TestObject;
use Magento\AcceptanceTestFramework\Test\Objects\CestHookObject;
use Magento\AcceptanceTestFramework\Test\TestDataParser;

class CestArrayProcessor
{
    private $parsedArray;
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";
    private static $cestDataManager;

    public static function getInstance()
    {
        if (!self::$cestDataManager) {
            $testDataParser = ObjectManagerFactory::getObjectManager()->create(TestDataParser::class);
            $parsedCestArray = $testDataParser->readTestData();
            self::$cestDataManager = new CestArrayProcessor($parsedCestArray);
        }

        return self::$cestDataManager;
    }

    private function __construct($parsedCestArray)
    {
        $this->parsedArray = $parsedCestArray;
    }

    /**
     * This method takes no arguments and returns CestObjects parsed from defined *Cest.xml files.
     *
     * @return array
     */
    public function getCestData()
    {
        $cests = [];

        foreach ($this->parsedArray[CestDataConstants::CEST_ROOT] as $cestName => $cestData) {
            $hooks = [];
            $annotations = [];

            if (!is_array($cestData)) {
                continue;
            }

            $tests = $this->stripDescriptorTags(
                $cestData,
                CestDataConstants::NODE_NAME,
                CestDataConstants::NAME
            );

            if (array_key_exists(CestDataConstants::CEST_BEFORE_HOOK, $cestData)) {
                $hooks[CestDataConstants::CEST_BEFORE_HOOK] = $this->extractHook(
                    CestDataConstants::CEST_BEFORE_HOOK,
                    $cestData[CestDataConstants::CEST_BEFORE_HOOK]
                );

                $tests = $this->stripDescriptorTags($tests, CestDataConstants::CEST_BEFORE_HOOK);
            }

            if (array_key_exists(CestDataConstants::CEST_AFTER_HOOK, $cestData)) {
                $hooks[CestDataConstants::CEST_AFTER_HOOK] = $this->extractHook(
                    CestDataConstants::CEST_AFTER_HOOK,
                    $cestData[CestDataConstants::CEST_AFTER_HOOK]
                );

                $tests = $this->stripDescriptorTags($tests, CestDataConstants::CEST_AFTER_HOOK);
            }

            if (array_key_exists(CestDataConstants::CEST_ANNOTATIONS, $cestData)) {
                $annotations = $this->extractAnnotations($cestData[CestDataConstants::CEST_ANNOTATIONS]);

                $tests = $this->stripDescriptorTags($tests, CestDataConstants::CEST_ANNOTATIONS);
            }


            $cests[] = new CestObject(
                $cestName,
                $annotations,
                $this->extractTestData($tests),
                $hooks
            );
        }

        return $cests;
    }

    /**
     * This method trims all irrelevant tags to extract hook information including before and after tags
     * and their relevant actions. The result is an array of CestHookObjects.
     *
     * @param string $hookType
     * @param array $cestHook
     * @return array
     */
    private function extractHook($hookType, $cestHook)
    {
        $hookActions = $this->stripDescriptorTags(
            $cestHook,
            CestDataConstants::NODE_NAME
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
        $annotations = $this->stripDescriptorTags($cestAnnotations, CestDataConstants::NODE_NAME);

        // parse the Cest annotations
        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationValues = [];
            foreach ($annotationData as $annotationValue) {
                $annotationValues[] = $annotationValue[CestDataConstants::ANNOTATION_VALUE];
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
                CestDataConstants::NODE_NAME,
                CestDataConstants::NAME,
                CestDataConstants::TEST_ANNOTATIONS
            );

            if (array_key_exists(CestDataConstants::TEST_ANNOTATIONS, $testData)) {
                $testAnnotations = $this->extractAnnotations($testData[CestDataConstants::TEST_ANNOTATIONS]);
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
            $mergeKey = $actionData[CestDataConstants::TEST_STEP_MERGE_KEY];
            $actionAttributes = $this->stripDescriptorTags(
                $actionData,
                CestDataConstants::TEST_STEP_MERGE_KEY,
                CestDataConstants::NODE_NAME
            );
            $linkedAction = null;
            $order = null;

            if (array_key_exists(CestDataConstants::TEST_ACTION_BEFORE, $actionData)
                and array_key_exists(CestDataConstants::TEST_ACTION_AFTER, $actionData)) {
                throw new XmlException(sprintf(self::BEFORE_AFTER_ERROR_MSG, $actionName));
            }

            if (array_key_exists(CestDataConstants::TEST_ACTION_BEFORE, $actionData)) {
                $linkedAction = $actionData[CestDataConstants::TEST_ACTION_BEFORE];
                $order = "before";
            } elseif (array_key_exists(CestDataConstants::TEST_ACTION_AFTER, $actionData)) {
                $linkedAction = $actionData[CestDataConstants::TEST_ACTION_AFTER];
                $order = "after";
            }

            // TODO this is to be implemented later. Currently the schema does not use or need return var.
            /*if (array_key_exists(CestDataConstants::TEST_ACTION_RETURN_VARIABLE, $actionData)) {
                $returnVariable = $actionData[CestDataConstants::TEST_ACTION_RETURN_VARIABLE];
            }*/

            $actions[] = new ActionObject(
                $mergeKey,
                $actionData[CestDataConstants::NODE_NAME],
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
     * @param array ...$tags
     * @return mixed
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
