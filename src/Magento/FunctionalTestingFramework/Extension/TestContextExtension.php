<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use \Codeception\Events;

/**
 * Class TestContextExtension
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class TestContextExtension extends \Codeception\Extension
{
    /**
     * Codeception Events Mapping to methods
     * @var array
     */
    public static $events = [
        Events::TEST_BEFORE => 'beforeTest',
    ];

    /**
     * Static variable for keeping track of test phase.
     * @var string
     */
    private static $testPhase;

    const TEST_PHASE_BEFORE = "before";
    const TEST_PHASE_TEST = "test";
    const TEST_PHASE_AFTER = "after";

    /**
     * Codeception event listener function, triggered on activation of test execution.
     * @return void
     */
    public function beforeTest()
    {
        TestContextExtension::$testPhase = TestContextExtension::TEST_PHASE_BEFORE;
    }

    /**
     * Public setter for testPhase
     * @param string $testPhase
     * @return void
     */
    public static function setTestPhase(string $testPhase)
    {
        TestContextExtension::$testPhase = $testPhase;
    }

    /**
     * Getter for testPhase
     * @return string
     */
    public static function getTestPhase()
    {
        return TestContextExtension::$testPhase;
    }
}
