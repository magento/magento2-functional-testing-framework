<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\CestHookObject;

/**
 * Class CestHookObjectExtractor
 */
class CestHookObjectExtractor extends BaseCestObjectExtractor
{
    /**
     * Action Object Extractor object
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * Test Entity Extractor object
     *
     * @var TestEntityExtractor
     */
    private $testEntityExtractor;

    /**
     * CestHookObjectExtractor constructor
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
        $this->testEntityExtractor = new TestEntityExtractor();
    }

    /**
     * This method trims all irrelevant tags to extract hook information including before and after tags
     * and their relevant actions. The result is an array of CestHookObjects.
     *
     * @param string $parentName
     * @param string $hookType
     * @param array $cestHook
     * @return CestHookObject
     */
    public function extractHook($parentName, $hookType, $cestHook)
    {
        $hookActions = $this->stripDescriptorTags(
            $cestHook,
            self::NODE_NAME
        );

        $hook = new CestHookObject(
            $hookType,
            $parentName,
            $this->actionObjectExtractor->extractActions($hookActions),
            $this->testEntityExtractor->extractTestEntities($hookActions)
        );

        return $hook;
    }
}
