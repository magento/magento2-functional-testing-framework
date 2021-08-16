<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use tests\unit\Util\MagentoTestCase;

class FilePathFormatterTest extends MagentoTestCase
{
    /**
     * Test file format.
     *
     * @param string $path
     * @param bool $withTrailingSeparator
     * @param string|null $expectedPath
     *
     * @return void
     * @throws TestFrameworkException
     * @dataProvider formatDataProvider
     */
    public function testFormat(string $path, bool $withTrailingSeparator, ?string $expectedPath): void
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
     * Test file format with exception.
     *
     * @param string $path
     * @param bool $withTrailingSeparator
     *
     * @return void
     * @throws TestFrameworkException
     * @dataProvider formatExceptionDataProvider
     */
    public function testFormatWithException(string $path, bool $withTrailingSeparator): void
    {
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Invalid or non-existing file: $path\n");
        FilePathFormatter::format($path, $withTrailingSeparator);
    }

    /**
     * Data input.
     *
     * @return array
     */
    public function formatDataProvider(): array
    {
        $path1 = rtrim(TESTS_BP, '/');
        $path2 = $path1 . DIRECTORY_SEPARATOR;

        return [
            [$path1, false, $path1],
            [$path1, false, $path1],
            [$path1, true, $path2],
            [$path2, false, $path1],
            [$path2, false, $path1],
            [$path2, true, $path2],
            [__DIR__. DIRECTORY_SEPARATOR . basename(__FILE__), false, __FILE__],
            ['', false, null] // Empty string is valid
        ];
    }

    /**
     * Invalid data input.
     *
     * @return array
     */
    public function formatExceptionDataProvider(): array
    {
        return [
            ['abc', false],
            ['X://some\dir/@', false]
        ];
    }
}
