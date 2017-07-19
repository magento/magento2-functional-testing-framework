<?php

namespace Magento\AcceptanceTestFramework\Test;

use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\Exceptions\XmlException;

class CestDataManager
{
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";

    public static function getCestData()
    {
        $cestDataParser = ObjectManagerFactory::getObjectManager()->create(TestDataParser::class);
        return self::transform($cestDataParser->readTestData());
    }

    private static function transform($parsedArray)
    {
        $cests = [];

        foreach ($parsedArray[CestDataConstants::CEST_ROOT] as $cestName => $cestData) {
            $hooks = [];

            if (array_key_exists(CestDataConstants::CEST_BEFORE_HOOK, $cestData)) {
                $hooks[CestDataConstants::CEST_BEFORE_HOOK] = self::extractHook(
                    CestDataConstants::CEST_BEFORE_HOOK,
                    $cestData[CestDataConstants::CEST_BEFORE_HOOK]
                );
            }

            if (array_key_exists(CestDataConstants::CEST_AFTER_HOOK, $cestData)) {
                $hooks[CestDataConstants::CEST_AFTER_HOOK] = self::extractHook(
                    CestDataConstants::CEST_AFTER_HOOK,
                    $cestData[CestDataConstants::CEST_AFTER_HOOK]
                );
            }

            $cests[] = new CestObject(
                $cestName,
                self::extractAnnotations($cestData[CestDataConstants::CEST_ANNOTATIONS]),
                self::extractCestUseStatements($cestData[CestDataConstants::CEST_USE_STATEMENTS]),
                self::extractTestData($cestData[CestDataConstants::CEST_TEST_TAG]),
                $hooks
            );
        }

        return $cests;
    }

    private static function extractHook($hookType, $cestHook)
    {
        $hooks = [];

        foreach ($cestHook as $cestHookData) {
            $hooks[] = new CestHookObject(
                $hookType,
                self::extractTestDependencies($cestHookData[CestDataConstants::TEST_DEPENDENCY]),
                self::extractTestActions($cestHookData[CestDataConstants::TEST_ACTION])
            );
        }

        return $hooks;
    }

    private static function extractAnnotations($cestAnnotations)
    {
        $annotations = [];

        // parse the Cest annotations
        foreach ($cestAnnotations as $annotationData) {
            foreach ($annotationData as $annotationKey => $annotationValue) {
                $annotationValues = [];

                foreach ($annotationValue as $annotationValueKey => $annotation) {
                     $annotationValues[] = $annotation[CestDataConstants::ANNOTATION_VALUE];
                }

                $annotations[$annotationKey] = $annotationValues;
            }
        }

        return $annotations;
    }

    private static function extractCestUseStatements($cestUseStatements)
    {
        $useStatements = [];

        // parse any use statements
        foreach ($cestUseStatements as $cestUseStatement) {
            $useStatements[] = $cestUseStatement[CestDataConstants::CEST_USE_PATH];
        }

        return $useStatements;
    }

    private static function extractTestData($cestTestData)
    {
        $tests = [];

        // parse the tests
        foreach ($cestTestData as $testName => $testData) {
            $testAnnotations = [];

            if (array_key_exists(CestDataConstants::TEST_ANNOTATIONS, $testData)) {
                $testAnnotations = self::extractAnnotations($testData[CestDataConstants::TEST_ANNOTATIONS]);
            }

            $tests[] = new TestObject(
                $testName,
                self::extractTestDependencies($testData[CestDataConstants::TEST_DEPENDENCY]),
                self::extractTestActions($testData[CestDataConstants::TEST_ACTION]),
                $testAnnotations
            );
        }

        return $tests;
    }

    private static function extractTestDependencies($testDependencies)
    {
        $dependencies = [];

        foreach ($testDependencies as $dependencyName => $dependencyData) {
            $dependencies[$dependencyName] = $dependencyData[CestDataConstants::TEST_DEPENDENCY_ACTOR];
        }

        return $dependencies;
    }

    private static function extractTestActions($testActions)
    {
        $actions = [];

        foreach ($testActions as $actionName => $actionData) {
            $function = $actionData[CestDataConstants::TEST_ACTION_FUNCTION];
            $actor = null;
            $parameter = null;
            $selector = null;
            $userInput = null;
            $returnVariable = null;
            $linkedAction = null;
            $order = null;

            if (array_key_exists(CestDataConstants::TEST_ACTION_ACTOR, $actionData)) {
                $actor = $actionData[CestDataConstants::TEST_ACTION_ACTOR];
            }

            if (array_key_exists(CestDataConstants::TEST_ACTION_PARAMETER, $actionData)) {
                $parameter = $actionData[CestDataConstants::TEST_ACTION_PARAMETER];
            } elseif (array_key_exists(CestDataConstants::TEST_ACTION_SELECTOR, $actionData)) {
                $selector = $actionData[CestDataConstants::TEST_ACTION_SELECTOR];
            }

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

            if (array_key_exists(CestDataConstants::TEST_ACTION_USER_INPUT, $actionData)) {
                $userInput = $actionData[CestDataConstants::TEST_ACTION_USER_INPUT];
            }

            if (array_key_exists(CestDataConstants::TEST_ACTION_RETURN_VARIABLE, $actionData)) {
                $returnVariable = $actionData[CestDataConstants::TEST_ACTION_RETURN_VARIABLE];
            }

            $actions[] = new ActionObject(
                $actionName,
                $actor,
                $function,
                $selector,
                $parameter,
                $order,
                $linkedAction,
                $returnVariable,
                $userInput
            );
        }

        return $actions;
    }
}
