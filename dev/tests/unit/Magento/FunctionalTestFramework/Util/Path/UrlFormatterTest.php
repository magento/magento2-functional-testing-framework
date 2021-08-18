<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;
use tests\unit\Util\MagentoTestCase;

class UrlFormatterTest extends MagentoTestCase
{
    /**
     * Test url format.
     *
     * @param string $path
     * @param bool|null $withTrailingSeparator
     * @param string $expectedPath
     *
     * @return void
     * @dataProvider formatDataProvider
     */
    public function testFormat(string $path, ?bool $withTrailingSeparator, string $expectedPath): void
    {
        if ($withTrailingSeparator === null) {
            $this->assertEquals($expectedPath, UrlFormatter::format($path));
            return;
        }
        $this->assertEquals($expectedPath, UrlFormatter::format($path, $withTrailingSeparator));
    }

    /**
     * Test url format with exception.
     *
     * @param string $path
     * @param bool|null $withTrailingSeparator
     *
     * @return void
     * @dataProvider formatExceptionDataProvider
     */
    public function testFormatWithException(string $path, ?bool $withTrailingSeparator): void
    {
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Invalid url: $path\n");

        if ($withTrailingSeparator === null) {
            UrlFormatter::format($path);
            return;
        }
        UrlFormatter::format($path, $withTrailingSeparator);
    }

    /**
     * Data input.
     *
     * @return array
     */
    public function formatDataProvider(): array
    {
        $url1 = 'http://magento.local/index.php';
        $url2 = $url1 . '/';
        $url3 = 'https://www.example.com/index.php/admin';
        $url4 = $url3 . '/';
        $url5 = 'www.google.com';
        $url6 = 'http://www.google.com/';
        $url7 = 'http://127.0.0.1:8200';
        $url8 = 'wwøw.goåoøgle.coøm';
        $url9 = 'http://www.google.com';

        return [
            [$url1, null, $url2],
            [$url1, false, $url1],
            [$url1, true, $url2],
            [$url2, null, $url2],
            [$url2, false, $url1],
            [$url2, true, $url2],
            [$url3, null, $url4],
            [$url3, false, $url3],
            [$url3, true, $url4],
            [$url4, null, $url4],
            [$url4, false, $url3],
            [$url4, true, $url4],
            [$url5, true, $url6],
            [$url7, false, $url7],
            [$url8, false, $url9],
            ['https://magento.local/path?', false, 'https://magento.local/path?'],
            ['https://magento.local/path#', false, 'https://magento.local/path#'],
            ['https://magento.local/path?#', false, 'https://magento.local/path?#']
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
            ['', null]
        ];
    }
}
