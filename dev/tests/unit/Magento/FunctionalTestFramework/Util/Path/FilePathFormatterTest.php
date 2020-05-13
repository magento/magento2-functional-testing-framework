<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

class FilePathFormatterTest extends MagentoTestCase
{
    /**
     * Test file format
     *
     * @dataProvider formatDataProvider
     * @param string $path
     * @param boolean $withTrailingSeparator
     * @param mixed string|boolean $expectedPath
     * @return void
     * @throws TestFrameworkException
     */
    public function testFormat($path, $withTrailingSeparator, $expectedPath)
    {
        if (null !== $expectedPath) {
            $this->assertEquals($expectedPath, FilePathFormatter::format($path, $withTrailingSeparator));
        } else {
            // Assert no exception
            FilePathFormatter::format($path, $withTrailingSeparator);
            $this->assertTrue(true);
        }
    }

    /**
     * Test file format with exception
     *
     * @dataProvider formatExceptionDataProvider
     * @param string $path
     * @param boolean $withTrailingSeparator
     * @return void
     * @throws TestFrameworkException
     */
    public function testFormatWithException($path, $withTrailingSeparator)
    {
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Invalid or non-existing file: $path\n");
        FilePathFormatter::format($path, $withTrailingSeparator);
    }

    /**
     * Data input
     *
     * @return array
     */
    public function formatDataProvider()
    {
        $path1 = rtrim(TESTS_BP, '/');
        $path2 = $path1 . DIRECTORY_SEPARATOR;
        return [
            [$path1, null, $path1],
            [$path1, false, $path1],
            [$path1, true, $path2],
            [$path2, null, $path1],
            [$path2, false, $path1],
            [$path2, true, $path2],
            [__DIR__. DIRECTORY_SEPARATOR . basename(__FILE__), null, __FILE__],
            ['', null, null] // Empty string is valid
        ];
    }

    /**
     * Invalid data input
     *
     * @return array
     */
    public function formatExceptionDataProvider()
    {
        return [
            ['abc', null],
            ['X://some\dir/@', null],
        ];
    }
}
