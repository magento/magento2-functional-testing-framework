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
     * @param string      $path
     * @param bool|null   $withTrailingSeparator
     * @param string|null $expectedPath
     *
     * @return void
     * @throws TestFrameworkException
     * @dataProvider formatDataProvider
     */
    public function testFormat(string $path, ?bool $withTrailingSeparator, ?string $expectedPath): void
    {
        if (null !== $expectedPath) {
            if ($withTrailingSeparator === null) {
                $this->assertEquals($expectedPath, FilePathFormatter::format($path));
                return;
            }
            $this->assertEquals($expectedPath, FilePathFormatter::format($path, $withTrailingSeparator));
        } else {
            // Assert no exception
            if ($withTrailingSeparator === null) {
                FilePathFormatter::format($path);
            } else {
                FilePathFormatter::format($path, $withTrailingSeparator);
            }
            $this->assertTrue(true);
        }
    }

    /**
     * Test file format with exception.
     *
     * @param string    $path
     * @param bool|null $withTrailingSeparator
     *
     * @return void
     * @throws TestFrameworkException
     * @dataProvider formatExceptionDataProvider
     */
    public function testFormatWithException(string $path, ?bool $withTrailingSeparator): void
    {
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Invalid or non-existing file: $path\n");

        if ($withTrailingSeparator === null) {
            FilePathFormatter::format($path);
            return;
        }
        FilePathFormatter::format($path, $withTrailingSeparator);
    }

    /**
     * Data input.
     *
     * @return array
     */
    public static function formatDataProvider(): array
    {
        $path1 = rtrim(TESTS_BP, '/');
        $path2 = $path1 . DIRECTORY_SEPARATOR;

        return [
            [$path1, null, $path2],
            [$path1, false, $path1],
            [$path1, true, $path2],
            [$path2, null, $path2],
            [$path2, false, $path1],
            [$path2, true, $path2],
            [__DIR__ . DIRECTORY_SEPARATOR . basename(__FILE__), null, __FILE__ . DIRECTORY_SEPARATOR],
            ['', null, null] // Empty string is valid
        ];
    }

    /**
     * Invalid data input.
     *
     * @return array
     */
    public static function formatExceptionDataProvider(): array
    {
        return [
            ['abc', null],
            ['X://some\dir/@', null]
        ];
    }
}
