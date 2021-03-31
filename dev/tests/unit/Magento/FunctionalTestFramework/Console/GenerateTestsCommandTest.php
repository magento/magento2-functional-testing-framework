<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Console;

use PHPUnit\Framework\TestCase;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Console\GenerateTestsCommand;

class GenerateTestsCommandTest extends TestCase
{
    /**
     * @param mixed $time
     * @param mixed $groups
     * @param mixed $expected
     * @return void
     * @dataProvider configParallelOptions
     */
    public function testParseConfigParallelOptions($time, $groups, $expected): void
    {
        $command = new GenerateTestsCommand();
        $class = new \ReflectionClass($command);
        $method = $class->getMethod('parseConfigParallelOptions');
        $method->setAccessible(true);

        if (is_array($expected)) {
            $actual = $method->invokeArgs($command, [$time, $groups]);
            $this->assertEquals($expected, $actual);
        } else {
            $this->expectException(FastFailException::class);
            $this->expectExceptionMessage($expected);
            $method->invokeArgs($command, [$time, $groups]);
        }
    }

    /**
     * Data provider for testParseConfigParallelOptions()
     *
     * @return array
     */
    public function configParallelOptions(): array
    {
        return [
            [null, null, ['parallelByTime', 600000]],   /* #0 */
            ['20', null, ['parallelByTime', 1200000]],  /* #1 */
            [5, null, ['parallelByTime', 300000]],      /* #2 */
            [null, '300', ['parallelByGroup', 300]],    /* #3 */
            [null, 1000, ['parallelByGroup', 1000]],    /* #4 */
            [0.5, null, "'time' option must be an integer and greater than 0"],     /* #5 */
            [0, null, "'time' option must be an integer and greater than 0"],       /* #6 */
            ['0', null, "'time' option must be an integer and greater than 0"],     /* #7 */
            [0.0, null, "'time' option must be an integer and greater than 0"],     /* #8 */
            ['0.0', null, "'time' option must be an integer and greater than 0"],   /* #9 */
            [-10, null, "'time' option must be an integer and greater than 0"],     /* #10 */
            ['-10', null, "'time' option must be an integer and greater than 0"],   /* #11 */
            ['12x', null, "'time' option must be an integer and greater than 0"],   /* #12 */
            [null, 0.5, "'groups' option must be an integer and greater than 0"],   /* #13 */
            [null, 0, "'groups' option must be an integer and greater than 0"],     /* #14 */
            [null, 0.0, "'groups' option must be an integer and greater than 0"],   /* #15 */
            [null, '0', "'groups' option must be an integer and greater than 0"],   /* #16 */
            [null, '0.0', "'groups' option must be an integer and greater than 0"], /* #17 */
            [null, -10, "'groups' option must be an integer and greater than 0"],   /* #18 */
            [null, '-10', "'groups' option must be an integer and greater than 0"], /* #19 */
            [null, '12x', "'groups' option must be an integer and greater than 0"], /* #20 */
            ['20', '300', "'time' and 'groups' options are mutually exclusive. "
                . "Only one can be specified for 'config parallel'"
            ],                                                                      /* #21 */
            [20, 300, "'time' and 'groups' options are mutually exclusive. "
                . "Only one can be specified for 'config parallel'"
            ],                                                                      /* #22 */
            ['0', 0, "'time' and 'groups' options are mutually exclusive. "
                . "Only one can be specified for 'config parallel'"
            ],                                                                      /* #23 */
            [[1], null, "'time' option must be an integer and greater than 0"],     /* #24 */
            [null, [-1], "'groups' option must be an integer and greater than 0"],  /* #25 */
        ];
    }
}
