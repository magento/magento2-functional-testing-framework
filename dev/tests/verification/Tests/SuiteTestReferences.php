<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

class SuiteTestReferences
{
    /**
     * Array of suite to tests that the suite contains
     *
     * @var array
     */
    public static $data = [
        'functionalSuite1' => [
            'additionalTestCest.php',
            'additionalIncludeTest2Cest.php',
            'IncludeTest2Cest.php',
            'IncludeTestCest.php'
        ],
        'functionalSuiteHooks' => [
            'IncludeTestCest.php'
        ],
        'functionalSuite2' => [
            'additionalTestCest.php',
            'additionalIncludeTest2Cest.php',
            'IncludeTest2Cest.php',
            'IncludeTestCest.php'
        ],
        'suiteExtends' => [
            'ExtendedChildTestInSuiteCest.php'
        ],
        'functionalSuiteWithComments' => [
            'IncludeTestCest.php'
        ],
        'ActionsInDifferentModulesSuite' => [
            'IncludeActionsInDifferentModulesTestCest.php'
        ],
        'suiteWithMultiplePauseActionsSuite' => [
            'additionalTestCest.php',
            'ExcludeTest2Cest.php',
            'IncludeTest2Cest.php'
        ],
        'suiteWithPauseActionSuite' => [
            'additionalTestCest.php',
            'ExcludeTest2Cest.php',
            'IncludeTest2Cest.php'
        ],
        'PartialGenerateForIncludeSuite' => [
            'IncludeTestCest.php'
        ],
        'PartialGenerateNoExcludeSuite' => [
            'IncludeTestCest.php'
        ],
        'NotGenerateHookBeforeSuite' => [
        ],
        'NotGenerateHookAfterSuite' => [
        ],
        'deprecationCheckSuite' => [
            'DeprecationCheckDeprecatedTestCest.php',
            'DeprecationCheckTestCest.php'
        ],
        'NotGenerateEmptySuite' => [
        ],
    ];
}
