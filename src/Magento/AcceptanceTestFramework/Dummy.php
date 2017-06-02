<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework;

use Magento\AcceptanceTestFramework\Config\DataInterface;

/**
 * Class Dummy
 */
class Dummy
{
    public function __construct(DataInterface $pageObjects)
    {
        $this->pageObjects = $pageObjects;
    }

    public function readPageObjects()
    {
        var_dump($this->pageObjects->get('page/AdminProduct'));
    }
}
