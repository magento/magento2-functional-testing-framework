<?php
    /**
     * Copyright © Magento, Inc. All rights reserved.
     * See COPYING.txt for license details.
     */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Module\Util;

use Magento\FunctionalTestingFramework\Module\Util\ModuleUtils;
use PHPUnit\Framework\TestCase;

class ModuleUtilTest extends TestCase
{
    /**
     * Test utf8SafeControlCharacterTrim()
     *
     * @param string $input
     * @param string $output
     * @param string $removed
     *
     * @return void
     * @dataProvider inDataProvider
     */
    public function testUtf8SafeControlCharacterTrim(string $input, string $output, $removed): void
    {
        $util = new ModuleUtils();
        $this->assertStringContainsString($output, $util->utf8SafeControlCharacterTrim($input));
        $this->assertStringNotContainsString($removed, $util->utf8SafeControlCharacterTrim($input));
    }

    /**
     * Data input.
     *
     * @return array
     */
    public function inDataProvider(): array
    {
        $ctr1 = '';
        $ctr2 = '';
        $ctr3 = ' ';
        return [
            ["some text $ctr1", 'some text', $ctr1],
            ["some text $ctr2", 'some text', $ctr2],
            ["some text $ctr3", 'some text', $ctr3]
        ];
    }
}
