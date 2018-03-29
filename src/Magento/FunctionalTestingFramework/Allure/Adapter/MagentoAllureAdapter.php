<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

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
     * Variable name used for extracting group argument to codecept run commaned
     *
     * @var string
     */
    protected $groupKey = "groups";

    /**
     * Array of group values from test runner command to append to allure suitename
     *
     * @var array
     */
    protected $groups;

    /**
     * Initialize from parent with group value
     *
     * @params array $ignoredAnnotations
     * @return AllureAdapter
     */
    public function _initialize(array $ignoredAnnotations = [])
    {
        $this->groups = $this->getGroup($this->groupKey);
        parent::_initialize($ignoredAnnotations);
    }

    /**
     * Array of group values passed to test runner command
     *
     * @param String $groupKey
     * @return array
     */
    private function getGroup($groupKey)
    {
        $groups = $this->options[$groupKey];
        return $groups;
    }

    /**
     * Override of parent method to set suitename as suitename and group name concatenated
     *
     * @param SuiteEvent $suiteEvent
     * @return void
     */
    public function suiteBefore(SuiteEvent $suiteEvent)
    {
        $suite = $suiteEvent->getSuite();
        $group = implode(".", $this->groups);
        $suiteName = ($suite->getName())."-{$group}";

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

        // call parent function
        parent::suiteBefore($changeSuiteEvent);
    }


}

