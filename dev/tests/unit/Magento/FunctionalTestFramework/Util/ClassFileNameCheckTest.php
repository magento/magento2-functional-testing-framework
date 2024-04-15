<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\StaticCheck\ClassFileNamingCheck;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;

class ClassFileNameCheckTest extends MagentoTestCase
{
    /**
     * This Test checks if the file name is renamed to match the class name if  mismatch found in class and file name
     */
    public function testClassAndFileMismatchStaticCheckWhenViolationsFound()
    {
        $scriptUtil = new ScriptUtil();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $testXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "Test");
        $classFileNameCheck = new ClassFileNamingCheck();
        $result = $classFileNameCheck->findErrorsInFileSet($testXmlFiles, "test");
        $this->assertMatchesRegularExpression('/does not match with file name/', $result[array_keys($result)[0]][0]);
    }

    /**
     * This Test checks if the file name is renamed to match the class name if
     * mismatch not found in class and file name
     */
    public function testClassAndFileMismatchStaticCheckWhenViolationsNotFound()
    {
        $scriptUtil = new ScriptUtil();
        $modulePaths = $scriptUtil->getAllModulePaths();
        $testXmlFiles = $scriptUtil->getModuleXmlFilesByScope($modulePaths, "Page");
        $classFileNameCheck = new ClassFileNamingCheck();
        $result = $classFileNameCheck->findErrorsInFileSet($testXmlFiles, "page");
        $this->assertEquals(count($result), 0);
    }
}
