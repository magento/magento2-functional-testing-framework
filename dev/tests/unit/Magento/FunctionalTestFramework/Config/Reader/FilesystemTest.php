<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestFramework\Test\Config\Reader;

use Magento\FunctionalTestingFramework\Config\FileResolver\Module;
use Magento\FunctionalTestingFramework\Config\Reader\Filesystem;
use Magento\FunctionalTestingFramework\Config\ValidationState;
use Magento\FunctionalTestingFramework\Util\Iterator\File;
use PHPUnit\Framework\TestCase;
use AspectMock\Test as AspectMock;
use tests\unit\Util\TestLoggingUtil;

class FilesystemTest extends TestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * Test Reading Empty Files
     * @throws \Exception
     */
    public function testEmptyXmlFile()
    {
        // create mocked items and read the file
        $someFile = $this->setMockFile("somepath.xml", "");
        $filesystem = $this->createPseudoFileSystem($someFile);
        $filesystem->read();

        // validate log statement
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            "warning",
            "XML File is empty.",
            ["File" => "somepath.xml"]
        );
    }

    /**
     * Function used to set mock for File created in test
     *
     * @param string $fileName
     * @param string $content
     * @return object
     * @throws \Exception
     */
    public function setMockFile($fileName, $content)
    {
        $file = AspectMock::double(
            File::class,
            [
                'current' => "",
                'count' => 1,
                'getFilename' => $fileName
            ]
        )->make();

        //set mocked data property for File
        $property = new \ReflectionProperty(File::class, 'data');
        $property->setAccessible(true);
        $property->setValue($file, [$fileName => $content]);

        return $file;
    }

    /**
     * Function used to set mock for filesystem class during test
     *
     * @param string $fileList
     * @return object
     * @throws \Exception
     */
    public function createPseudoFileSystem($fileList)
    {
        $filesystem = AspectMock::double(Filesystem::class)->make();

        //set resolver to use mocked resolver
        $mockFileResolver = AspectMock::double(Module::class, ['get' => $fileList])->make();
        $property = new \ReflectionProperty(Filesystem::class, 'fileResolver');
        $property->setAccessible(true);
        $property->setValue($filesystem, $mockFileResolver);

        //set validator to use mocked validator
        $mockValidation = AspectMock::double(ValidationState::class, ['isValidationRequired' => false])->make();
        $property = new \ReflectionProperty(Filesystem::class, 'validationState');
        $property->setAccessible(true);
        $property->setValue($filesystem, $mockValidation);

        return $filesystem;
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
