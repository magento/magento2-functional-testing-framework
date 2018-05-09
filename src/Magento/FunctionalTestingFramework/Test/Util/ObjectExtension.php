<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;

class ObjectExtension
{

    /**
     * Resolves test references for extending parent objects
     *
     * @param TestObject|ActionGroupObject $extensionObject
     * @param array $parsedItems
     * @return array
     * @throws TestReferenceException|XmlException
     */
    public static function resolveReferencedExtensions($extensionObject, $parsedItems = null)
    {
        if ($extensionObject->getParentName() === null) {
            return $extensionObject->getActions();
        }
        if ($extensionObject instanceof TestObject) {
            return self::extendTest($extensionObject, $parsedItems);
        } elseif ($extensionObject instanceof ActionGroupObject) {
            return self::extendActionGroup($extensionObject, $parsedItems);
        } else {
            return $parsedItems;
        }

    }

    /**
     * Resolves test references for extending test objects
     *
     * @param TestObject $testObject
     * @param array $parsedSteps
     * @return array
     * @throws TestReferenceException|XmlException
     */
    private static function extendTest($testObject, $parsedSteps)
    {
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

        if ($parentTest->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend a test that already extends another test. Test: " . $parentTest->getName()
            );
        }
        echo("Extending Test: " . $parentTest->getName() . " => " . $testObject->getName());
        $referencedTestSteps = $parentTest->getOrderedActions();
        $newSteps = array_merge($referencedTestSteps, $parsedSteps);
        return $newSteps;
    }

    /**
     * Resolves test references for extending action group objects
     *
     * @param ActionGroupObject $actionGroupObject
     * @param array $parsedActions
     * @return array
     * @throws TestReferenceException|XmlException
     */
    private static function extendActionGroup($actionGroupObject, $parsedActions)
    {
        try {
            $parentActionGroup =
                ActionGroupObjectHandler::getInstance()->getObject($actionGroupObject->getParentName());
        } catch (TestReferenceException $error) {
            throw new XmlException(
                "Parent Action Group " .
                $actionGroupObject->getParentName() .
                " not defined for Test " .
                $actionGroupObject->getName() .
                "." .
                PHP_EOL
            );
        }

        if ($parentActionGroup->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend an action group that already extends another action group. " .
                $parentActionGroup->getName()
            );
        }
        echo("Extending Action Group: " .
            $parentActionGroup->getName() .
            " => " .
            $actionGroupObject->getName() .
            PHP_EOL
        );
        $referencedActions = $parentActionGroup->getActions();
        $newActions = array_merge($referencedActions, $parsedActions);
        return $newActions;
    }
}
