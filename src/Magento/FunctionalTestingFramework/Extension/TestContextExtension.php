<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension;

use \Codeception\Events;

/**
 * Class TestContextExtension
 */
class TestContextExtension extends \Codeception\Extension
{
    public static $events = [
        Events::TEST_BEFORE => 'beforeTest',
    ];

    const TEST_PHASE_BEFORE = "before";
    const TEST_PHASE_TEST = "test";
    const TEST_PHASE_AFTER = "after";

    private static $testPhase;

    /**
     * Codeception event listener function, triggered on activation of before hook.
     * @param \Codeception\Event\TestEvent $e
     */
    public function beforeTest(\Codeception\Event\TestEvent $e)
    {
        TestContextExtension::$testPhase = TestContextExtension::TEST_PHASE_BEFORE;
    }

    /**
     * Public setter for testPhase
     * @param string $testPhase
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