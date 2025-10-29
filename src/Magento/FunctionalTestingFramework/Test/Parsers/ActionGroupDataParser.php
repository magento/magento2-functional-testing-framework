<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Test\Parsers;

use Magento\FunctionalTestingFramework\Config\DataInterface;

/**
 * Class ActionGroupDataParser
 */

class ActionGroupDataParser
{
    /**
     * @var DataInterface
     */
    private $actionGroupData;

    /**
     * ActionGroupDataParser constructor.
     *
     * @param DataInterface $actionGroupData
     */
    public function __construct(DataInterface $actionGroupData)
    {
        $this->actionGroupData = $actionGroupData;
    }

    /**
     * Read action group xml and return as an array.
     *
     * @return array
     */
    public function readActionGroupData()
    {
        return $this->actionGroupData->get();
    }
}
