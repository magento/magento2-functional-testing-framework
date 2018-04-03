<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Adapter;

use Magento\FunctionalTestingFramework\Data\Argument\Interpreter\NullType;
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
    protected $group;

    /**
     * Initialize from parent with group value
     *
     * @param array $ignoredAnnotations
     * @return void
     */

    // @codingStandardsIgnoreStart
    public function _initialize(array $ignoredAnnotations = [])
    {
        $this->group = $this->getGroup($this->groupKey);
        parent::_initialize($ignoredAnnotations);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Array of group values passed to test runner command
     *
     * @param String $groupKey
     * @return String
     */
    private function getGroup($groupKey)
    {
        if(!($this->options[$groupKey] == Null)){
            return $this->options[$groupKey][0];
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

        if ($this->group != null) {
            $suite = $suiteEvent->getSuite();
            $suiteName = ($suite->getName()) . "-{$this->group}";

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
}
