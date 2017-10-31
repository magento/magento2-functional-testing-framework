<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Tests;

use Magento\FunctionalTestingFramework\Test\Handlers\CestObjectHandler;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use PHPUnit\Framework\TestCase;
use tests\verification\Util\FileDiffUtil;

class ParameterArrayTest extends TestCase
{
    const PARAMETER_ARRAY_CEST = 'ParameterArrayCest';
    const RESOURCES_PATH = __DIR__ . '/../Resources';

    /**
     * Tests flat generation of a hardcoded cest file with no external references.
     */
    public function testParameterArrayGeneration()
    {
        $cest = CestObjectHandler::getInstance()->getObject(self::PARAMETER_ARRAY_CEST);
        $test = TestGenerator::getInstance(null, [$cest]);
        $test->createAllCestFiles();

        $cestFile = $test->getExportDir() .
            DIRECTORY_SEPARATOR .
            self::PARAMETER_ARRAY_CEST .
            ".php";

        $this->assertTrue(file_exists($cestFile));

        $fileDiffUtil = new FileDiffUtil(
            self::RESOURCES_PATH . DIRECTORY_SEPARATOR . self::PARAMETER_ARRAY_CEST . ".txt",
            $cestFile
        );

        $diffResult = $fileDiffUtil->diffContents();
        $this->assertNull($diffResult, $diffResult);
    }
}
