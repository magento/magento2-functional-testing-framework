<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use AspectMock\Test as AspectMock;
use PHPUnit\Framework\TestCase;

/**
 * Class MagentoTestCase
 */
class MagentoTestCase extends TestCase
{
    /**
     * Teardown for removing AspectMock Double References
     */
    protected function tearDown()
    {
        AspectMock::clean();
    }
}
