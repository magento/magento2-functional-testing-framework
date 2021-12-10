<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Util;

use PHPUnit\Framework\TestCase;

/**
 * Class MagentoTestCase
 */
class MagentoTestCase extends TestCase
{
    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        if (!self::fileExists(DOCS_OUTPUT_DIR)) {
            mkdir(DOCS_OUTPUT_DIR, 0755, true);
        }

        parent::setUpBeforeClass();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        array_map('unlink', glob(DOCS_OUTPUT_DIR . DIRECTORY_SEPARATOR . "*"));

        if (file_exists(DOCS_OUTPUT_DIR)) {
            rmdir(DOCS_OUTPUT_DIR);
        }
    }
}
