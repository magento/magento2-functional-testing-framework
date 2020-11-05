<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class ObjectExtensionUtil
{
    /**
     * ObjectExtensionUtil constructor.
     */
    public function __construct()
    {
        // empty
    }

    /**
     * Resolves test references for extending test objects
     *
     * @param TestObject $testObject
     * @return TestObject
     * @throws TestFrameworkException
     * @throws XmlException
     */
    public function extendTest($testObject)
    {
        // Check to see if the parent test exists
        try {
            $parentTest = TestObjectHandler::getInstance()->getObject($testObject->getParentName());
        } catch (TestReferenceException $error) {
            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(ObjectExtensionUtil::class)->debug(
                    "parent test not defined. test will be skipped",
                    ["parent" => $testObject->getParentName(), "test" => $testObject->getName()]
                );
            }
            $skippedTest = $this->skipTest($testObject);
            return $skippedTest;
        }

        // Check to see if the parent test is already an extended test
        if ($parentTest->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend a test that already extends another test. Test: " . $parentTest->getName(),
                ["parent" => $parentTest->getName(), "actionGroup" => $testObject->getName()]
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(ObjectExtensionUtil::class)
                ->debug("extending test", ["parent" => $parentTest->getName(), "test" => $testObject->getName()]);
        }

        // Get steps for both the parent and the child tests
        $referencedTestSteps = $parentTest->getUnresolvedSteps();
        $newSteps = $this->processRemoveActions(array_merge($referencedTestSteps, $testObject->getUnresolvedSteps()));

        $testHooks = $this->resolveExtendedHooks($testObject, $parentTest);

        // Create new Test object to return
        $extendedTest = new TestObject(
            $testObject->getName(),
            $newSteps,
            $testObject->getAnnotations(),
            $testHooks,
            $testObject->getFilename(),
            $testObject->getParentName()
        );
        return $extendedTest;
    }

    /**
     * Resolves test references for extending action group objects
     *
     * @param ActionGroupObject $actionGroupObject
     * @return ActionGroupObject
     * @throws XmlException
     */
    public function extendActionGroup($actionGroupObject)
    {
        // Check to see if the parent action group exists
        $parentActionGroup = ActionGroupObjectHandler::getInstance()->getObject($actionGroupObject->getParentName());
        if ($parentActionGroup == null) {
            throw new XmlException(
                "Parent Action Group " .
                $actionGroupObject->getParentName() .
                " not defined for Test " .
                $actionGroupObject->getName() .
                "." .
                PHP_EOL
            );
        }

        // Check to see if the parent action group is already an extended action group
        if ($parentActionGroup->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend an action group that already extends another action group. " .
                $parentActionGroup->getName(),
                ["parent" => $parentActionGroup->getName(), "actionGroup" => $actionGroupObject->getName()]
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(ObjectExtensionUtil::class)->debug(
                "extending action group:",
                ["parent" => $parentActionGroup->getName(), "actionGroup" => $actionGroupObject->getName()]
            );
        }

        // Get steps for both the parent and the child action groups
        $referencedActions = $parentActionGroup->getActions();
        $newActions = $this->processRemoveActions(array_merge($referencedActions, $actionGroupObject->getActions()));

        $extendedArguments = array_merge(
            $actionGroupObject->getArguments(),
            $parentActionGroup->getArguments()
        );

        // Create new Action Group object to return
        $extendedActionGroup = new ActionGroupObject(
            $actionGroupObject->getName(),
            $actionGroupObject->getAnnotations(),
            $extendedArguments,
            $newActions,
            $actionGroupObject->getParentName(),
            $actionGroupObject->getFilename()
        );
        return $extendedActionGroup;
    }

        /**
         * Resolves test references for extending test objects
         *
         * @param TestObject $testObject
         * @param TestObject $parentTestObject
         * @return TestHookObject[] $testHooks
         */
    private function resolveExtendedHooks($testObject, $parentTestObject)
    {
        $testHooks = $testObject->getHooks();
        $parentHooks = $parentTestObject->getHooks();

        // Get the hooks for each Test merge changes from the child hooks to the parent hooks into the child hooks
        foreach ($testHooks as $key => $hook) {
            if (array_key_exists($key, $parentHooks)) {
                $testHookActions = array_merge(
                    $parentHooks[$key]->getUnresolvedActions(),
                    $testHooks[$key]->getUnresolvedActions()
                );
                $cleanedTestHookActions = $this->processRemoveActions($testHookActions);

                $newTestHook = new TestHookObject(
                    $parentHooks[$key]->getType(),
                    $parentHooks[$key]->getParentName(),
                    $cleanedTestHookActions
                );
                $testHooks[$key] = $newTestHook;
            } else {
                $testHooks[$key] = $hook;
            }
        }

        // Add parent hooks to child if they did not originally exist on the child
        foreach ($parentHooks as $key => $hook) {
            if (!array_key_exists($key, $testHooks)) {
                $testHooks[$key] = $hook;
            }
        }

        return $testHooks;
    }

    /**
     * Resolves test references for removing actions in extended test
     *
     * @param ActionObject[] $actions
     * @return ActionObject[]
     * @throws XmlException
     */
    private function processRemoveActions($actions)
    {
        $cleanedActions = [];

        // remove actions merged that are of type 'remove'
        foreach ($actions as $actionName => $actionData) {
            if ($actionData->getType() != "remove") {
                $cleanedActions[$actionName] = $actionData;
            }
        }

        return $cleanedActions;
    }

    /**
     * This method returns a skipped form of the Test Object
     *
     * @param TestObject $testObject
     * @return TestObject
     */
    public function skipTest($testObject)
    {
        $annotations = $testObject->getAnnotations();

        // Add skip to the group array if it doesn't already exist
        if (array_key_exists('skip', $annotations)) {
            return $testObject;
        } elseif (!array_key_exists('skip', $annotations)) {
            $annotations['skip'] = ['issueId' => "ParentTestDoesNotExist"];
        }

        $skippedTest = new TestObject(
            $testObject->getName(),
            [],
            $annotations,
            [],
            $testObject->getFilename(),
            $testObject->getParentName()
        );

        return $skippedTest;
    }
}
