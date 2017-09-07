<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;

/**
 * Class ActionGroupObjectExtractor
 */
class ActionGroupObjectExtractor extends BaseCestObjectExtractor
{
    const DEFAULT_ENTITY = 'defaultEntity';

    /**
     * Action Object Extractor for converting actions into objects
     *
     * @var ActionObjectExtractor
     */
    private $actionObjectExtractor;

    /**
     * ActionGroupObjectExtractor constructor.
     */
    public function __construct()
    {
        $this->actionObjectExtractor = new ActionObjectExtractor();
    }

    /**
     * Method to parse array of action group data into ActionGroupObject
     *
     * @param array $actionGroupData
     * @return ActionGroupObject
     */
    public function extractActionGroup($actionGroupData)
    {
        $actionData = $this->stripDescriptorTags(
            $actionGroupData,
            self::NODE_NAME,
            self::DEFAULT_ENTITY,
            self::NAME
        );

        $actions = $this->actionObjectExtractor->extractActions($actionData);

        return new ActionGroupObject(
            $actionGroupData[self::NAME],
            $actionGroupData[self::DEFAULT_ENTITY],
            $actions
        );
    }
}
