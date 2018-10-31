<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;
use PHPUnit\Framework\TestCase;

class ModulePathExtractorTest extends TestCase
{
    const EXTENSION_PATH = "app"
    . DIRECTORY_SEPARATOR
    . "code"
    . DIRECTORY_SEPARATOR
    . "TestExtension"
    . DIRECTORY_SEPARATOR
    . "[Analytics]"
    . DIRECTORY_SEPARATOR
    . "Test"
    . DIRECTORY_SEPARATOR
    . "Mftf"
    . DIRECTORY_SEPARATOR
    . "Test"
    . DIRECTORY_SEPARATOR
    . "SomeText.xml";

    const MAGENTO_PATH = "dev"
    . DIRECTORY_SEPARATOR
    . "tests"
    . DIRECTORY_SEPARATOR
    . "acceptance"
    . DIRECTORY_SEPARATOR
    . "tests"
    . DIRECTORY_SEPARATOR
    . "functional"
    . DIRECTORY_SEPARATOR
    . "Magento"
    . DIRECTORY_SEPARATOR
    . "FunctionalTest"
    . DIRECTORY_SEPARATOR
    . "[Analytics]"
    . DIRECTORY_SEPARATOR
    . "Test"
    . DIRECTORY_SEPARATOR
    . "SomeText.xml";

    /**
     * Validate correct module is returned for dev/tests path
     * @throws \Exception
     */
    public function testGetMagentoModule()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            '[Analytics]',
            $modulePathExtractor->extractModuleName(
                self::MAGENTO_PATH
            )
        );
    }

    /**
     * Validate correct module is returned for extension path
     * @throws \Exception
     */
    public function testGetExtensionModule()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            '[Analytics]',
            $modulePathExtractor->extractModuleName(
                self::EXTENSION_PATH
            )
        );
    }

    /**
     * Validate Magento is returned for dev/tests/acceptance
     * @throws \Exception
     */
    public function testMagentoModulePath()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            'Magento',
            $modulePathExtractor->getExtensionPath(
                self::MAGENTO_PATH
            )
        );
    }

    /**
     * Validate correct extension path is returned
     * @throws \Exception
     */
    public function testExtensionModulePath()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            'TestExtension',
            $modulePathExtractor->getExtensionPath(
                self::EXTENSION_PATH
            )
        );
    }
}
