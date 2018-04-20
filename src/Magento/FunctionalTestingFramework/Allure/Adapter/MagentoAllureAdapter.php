<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Magento\FunctionalTestingFramework\Data\Argument\Interpreter\NullType;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Yandex\Allure\Adapter\AllureAdapter;
use Codeception\Event\SuiteEvent;

/**
 * Class MagentoAllureAdapter
 *
 * Extends AllureAdapter to provide further information for allure reports
 *
 * @package Magento\FunctionalTestingFramework\Allure
 */

class MagentoAllureAdapter extends AllureAdapter
{
    /**
     * Array of group values passed to test runner command
     *
     * @return String
     */
    private function getGroup()
    {
        if ($this->options['groups'] != null) {
            return $this->options['groups'][0];
        }
        return null;
    }

    /**
     * Override of parent method to set suitename as suitename and group name concatenated
     *
     * @param SuiteEvent $suiteEvent
     * @return void
     */
    public function suiteBefore(SuiteEvent $suiteEvent)
    {
        $changeSuiteEvent = $suiteEvent;

        if ($this->getGroup() != null) {
            $suite = $suiteEvent->getSuite();
            $suiteName = ($suite->getName()) . "\\" . $this->sanitizeGroupName($this->getGroup());

            call_user_func(\Closure::bind(
                function () use ($suite, $suiteName) {
                    $suite->name = $suiteName;
                },
                null,
                $suite
            ));

            //change suiteEvent
            $changeSuiteEvent = new SuiteEvent(
                $suiteEvent->getSuite(),
                $suiteEvent->getResult(),
                $suiteEvent->getSettings()
            );
        }
        // call parent function
        parent::suiteBefore($changeSuiteEvent);
    }

    /**
     * Function which santizes any group names changed by the framework for execution in order to consolidate reporting.
     *
     * @param string $group
     * @return string
     */
    private function sanitizeGroupName($group)
    {
        $suiteNames = array_keys(SuiteObjectHandler::getInstance()->getAllObjects());
        $exactMatch = in_array($group, $suiteNames);

        // if this is an existing suite name we dont' need to worry about changing it
        if ($exactMatch || strpos($group, "_") === false) {
            return $group;
        }

        // if we can't find this group in the generated suites we have to assume that the group was split for generation
        $groupNameSplit = explode("_", $group);
        array_pop($groupNameSplit);
        $originalName = implode("_", $groupNameSplit);

        // confirm our original name is one of the existing suite names otherwise just return the original group name
        $originalName = in_array($originalName, $suiteNames) ? $originalName : $group;
        return $originalName;
    }
}
