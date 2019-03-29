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
    public static function setUpBeforeClass()
    {
        if (!self::fileExists(OUTPUT_DIR)) {
            mkdir(OUTPUT_DIR, 0755, true);
        }
        parent::setUpBeforeClass();
    }

    /**
     * Teardown for removing AspectMock Double References
     * @return void
     */
    public static function tearDownAfterClass()
    {
        AspectMock::clean();
        array_map('unlink', glob(OUTPUT_DIR . DIRECTORY_SEPARATOR . "*"));
        if (file_exists(OUTPUT_DIR)) {
            rmdir(OUTPUT_DIR);
        }
    }
}
