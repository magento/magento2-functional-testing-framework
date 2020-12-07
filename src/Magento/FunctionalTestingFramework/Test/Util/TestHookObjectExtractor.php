<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
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
     * @param array  $testHook
     * @return TestHookObject
     * @throws XmlException
     * @throws TestReferenceException
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

    /**
     * Creates the default failed hook object with a single saveScreenshot action.
     * And a pause action when ENABLE_PAUSE is set to true.
     *
     * @param string $parentName
     * @return TestHookObject
     */
    public function createDefaultFailedHook($parentName)
    {
        $defaultSteps['saveScreenshot'] = new ActionObject("saveScreenshot", "saveScreenshot", []);
        if (getenv('ENABLE_PAUSE') === 'true') {
            $defaultSteps['pauseWhenFailed'] = new ActionObject(
                'pauseWhenFailed',
                'pause',
                [ActionObject::PAUSE_ACTION_INTERNAL_ATTRIBUTE => true]
            );
        }

        $hook = new TestHookObject(
            TestObjectExtractor::TEST_FAILED_HOOK,
            $parentName,
            $defaultSteps
        );

        return $hook;
    }
}
