<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

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
     * @throws TestReferenceException|XmlException
     */
    public function extendTest($testObject)
    {
        // Check to see if the parent test exists
        try {
            $parentTest = TestObjectHandler::getInstance()->getObject($testObject->getParentName());
        } catch (TestReferenceException $error) {
            throw new XmlException(
                "Parent Test " .
                $testObject->getParentName() .
                " not defined for Test " .
                $testObject->getName() .
                "." .
                PHP_EOL
            );
        }

        // Check to see if the parent test is already an extended test
        if ($parentTest->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend a test that already extends another test. Test: " . $parentTest->getName()
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Test: " . $parentTest->getName() . " => " . $testObject->getName() . PHP_EOL);
        }

        // Get steps for both the parent and the child tests
        $referencedTestSteps = $parentTest->getUnresolvedSteps();
        $newSteps = array_merge($referencedTestSteps, $testObject->getUnresolvedSteps());

        $testHooks = $testObject->getHooks();
        $parentHooks = $parentTest->getHooks();

        // Get the hooks for each Test merge changes from the child hooks to the parent hooks into the child hooks
        foreach ($testHooks as $key => $hook) {
            if (array_key_exists($key, $parentHooks)) {
                $testHookActions = array_merge(
                    $parentHooks[$key]->getUnresolvedActions(),
                    $testHooks[$key]->getUnresolvedActions()
                );
                $newTestHook = new TestHookObject(
                    $parentHooks[$key]->getType(),
                    $parentHooks[$key]->getParentName(),
                    $testHookActions
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
                $parentActionGroup->getName()
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Action Group: " .
                $parentActionGroup->getName() .
                " => " .
                $actionGroupObject->getName() .
                PHP_EOL
            );
        }

        // Get steps for both the parent and the child action groups
        $referencedActions = $parentActionGroup->getActions();
        $newActions = array_merge($referencedActions, $actionGroupObject->getActions());
        $extendedArguments = array_merge(
            $actionGroupObject->getArguments(),
            $parentActionGroup->getArguments()
        );

        // Create new Action Group object to return
        $extendedActionGroup = new ActionGroupObject(
            $actionGroupObject->getName(),
            $extendedArguments,
            $newActions,
            $actionGroupObject->getParentName()
        );
        return $extendedActionGroup;
    }
}
