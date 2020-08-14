<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\util;

use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

class MftfStaticTestCase extends TestCase
{
    const STATIC_RESULTS_DIR  = TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    '_output' .
    DIRECTORY_SEPARATOR .
    'static-results';

    const RESOURCES_PATH =   TESTS_MODULE_PATH .
    DIRECTORY_SEPARATOR .
    "Resources" .
    DIRECTORY_SEPARATOR .
    'StaticChecks';

    /**
     * Sets input interface
     * @param $path
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function mockInputInterface($path = null)
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        if ($path) {
            $input->method('getOption')
                ->with('path')
                ->willReturn($path);
        }
        return $input;
    }

    public function tearDown(): void
    {
        DirSetupUtil::rmdirRecursive(self::STATIC_RESULTS_DIR);
    }
}
