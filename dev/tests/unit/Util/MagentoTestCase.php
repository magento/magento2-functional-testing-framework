<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Util;

use AspectMock\Test as AspectMock;
use PHPUnit\Framework\TestCase;

/**
 * Class MagentoTestCase
 */
class MagentoTestCase extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!self::fileExists(DOCS_OUTPUT_DIR)) {
            mkdir(DOCS_OUTPUT_DIR, 0755, true);
        }
        parent::setUpBeforeClass();
    }

    /**
     * Teardown for removing AspectMock Double References
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        AspectMock::clean();
        array_map('unlink', glob(DOCS_OUTPUT_DIR . DIRECTORY_SEPARATOR . "*"));
        if (file_exists(DOCS_OUTPUT_DIR)) {
            rmdir(DOCS_OUTPUT_DIR);
        }
    }
}
