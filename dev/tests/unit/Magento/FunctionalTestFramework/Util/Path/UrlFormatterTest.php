<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Util\Path;

use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class UrlFormatterTest extends MagentoTestCase
{
    /**
     * Test url format
     *
     * @dataProvider formatDataProvider
     * @param string $path
     * @param boolean $withTrailingSeparator
     * @param mixed string|boolean $expectedPath
     * @return void
     */
    public function testFormat($path, $withTrailingSeparator, $expectedPath)
    {
        $this->assertEquals($expectedPath, UrlFormatter::format($path, $withTrailingSeparator));
    }

    /**
     * Test url format with exception
     *
     * @dataProvider formatExceptionDataProvider
     * @param string $path
     * @param boolean $withTrailingSeparator
     * @return void
     */
    public function testFormatWithException($path, $withTrailingSeparator)
    {
        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("Invalid url: $path\n");
        UrlFormatter::format($path, $withTrailingSeparator);
    }

    /**
     * Data input
     *
     * @return array
     */
    public function formatDataProvider()
    {
        $url1 = 'http://magento.local/index.php';
        $url2 = $url1 . '/';
        $url3 = 'https://www.example.com/index.php/admin';
        $url4 = $url3 . '/';
        $url5 = 'www.google.com';
        $url6 = 'http://www.google.com/';
        return [
            [$url1, null, $url1],
            [$url1, false, $url1],
            [$url1, true, $url2],
            [$url2, null, $url1],
            [$url2, false, $url1],
            [$url2, true, $url2],
            [$url3, null, $url3],
            [$url3, false, $url3],
            [$url3, true, $url4],
            [$url4, null, $url3],
            [$url4, false, $url3],
            [$url4, true, $url4],
            [$url5, true, $url6],
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
            ['', null],
        ];
    }
}
