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
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\Setup\Exception;

class ObjectExtension
{

    /**
     * Resolves test references for extending test objects
     *
     * @param TestObject $testObject
     * @return TestObject
     * @throws TestReferenceException|XmlException
     */
    public static function extendTest($testObject)
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
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Test: " . $parentTest->getName() . " => " . $testObject->getName() . PHP_EOL);
        }
        $referencedTestSteps = $parentTest->getOrderedActions();
        $newSteps = array_merge($referencedTestSteps, $testObject->getOrderedActions());
        $extendedTest = new TestObject(
            $testObject->getName(),
            $newSteps,
            $testObject->getAnnotations(),
            $testObject->getHooks(),
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
    public static function extendActionGroup($actionGroupObject)
    {
        try {
            $parentActionGroup =
                ActionGroupObjectHandler::getInstance()->getObject($actionGroupObject->getParentName());
        } catch (XmlException $error) {
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
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Action Group: " .
                $parentActionGroup->getName() .
                " => " .
                $actionGroupObject->getName() .
                PHP_EOL
            );
        }

        $referencedActions = $parentActionGroup->getActions();
        $newActions = array_merge($referencedActions, $actionGroupObject->getActions());
        $extendedArguments = array_merge(
            $actionGroupObject->getArguments(),
            $parentActionGroup->getArguments()
        );

        $extendedActionGroup = new ActionGroupObject(
            $actionGroupObject->getName(),
            $extendedArguments,
            $newActions,
            $actionGroupObject->getParentName()
        );
        return $extendedActionGroup;
    }
}
