<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;

/**
 * Class TestHookObjectExtractor
 */
class TestHookObjectExtractor extends BaseObjectExtractor
{
    /**
     * Action Object Extractor object
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * TestHookObjectExtractor constructor
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
    }

    /**
     * This method trims all irrelevant tags to extract hook information including before and after tags
     * and their relevant actions. The result is an array of TestHookObjects.
     *
     * @param string $parentName
     * @param string $hookType
     * @param array $testHook
     * @return TestHookObject
     */
    public function extractHook($parentName, $hookType, $testHook)
    {
        $hookActions = $this->stripDescriptorTags(
            $testHook,
            self::NODE_NAME
        );

        $hook = new TestHookObject(
            $hookType,
            $parentName,
            $this->actionObjectExtractor->extractActions($hookActions)
        );

        return $hook;
    }

    public function createDefaultFailedHook($parentName)
    {

        $saveScreenshotStep = ["saveScreenshot" => new ActionObject("saveScreenshot", "saveScreenshot", [])];

        $hook = new TestHookObject(
            TestObjectExtractor::TEST_FAILED_HOOK,
            $parentName,
            $saveScreenshotStep
        );

        return $hook;
    }
}
