<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Config\Reader;

use Magento\FunctionalTestingFramework\Config\ConverterInterface;
use Magento\FunctionalTestingFramework\Config\FileResolver\Module;
use Magento\FunctionalTestingFramework\Config\Reader\Filesystem;
use Magento\FunctionalTestingFramework\Config\SchemaLocatorInterface;
use Magento\FunctionalTestingFramework\Config\ValidationState;
use Magento\FunctionalTestingFramework\Util\Iterator\File;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use tests\unit\Util\TestLoggingUtil;

class FilesystemTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * @throws \Exception
     */
    public function testEmptyXmlFile()
    {
        $filesystem = $this->getFilesystem($this->getFileIterator('somepath.xml', ''));
        $this->assertEquals([], $filesystem->read());

        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'warning',
            'XML File is empty.',
            ['File' => 'somepath.xml']
        );
    }

    /**
     * Retrieve mocked file iterator
     *
     * @param string $fileName
     * @param string $content
     * @return File|MockObject
     * @throws \Exception
     */
    public function getFileIterator(string $fileName, string $content): File
    {
        $iterator = new \ArrayIterator([$content]);

        $file = $this->createMock(File::class);

        $file->method('current')
            ->willReturn($content);
        $file->method('getFilename')
            ->willReturn($fileName);
        $file->method('count')
            ->willReturn(1);

        $file->method('next')
            ->willReturnCallback(function () use ($iterator): void {
                $iterator->next();
            });

        $file->method('valid')
            ->willReturnCallback(function () use ($iterator): bool {
                return $iterator->valid();
            });

        return $file;
    }

    /**
     * Get real instance of Filesystem class with mocked dependencies
     *
     * @param File $fileIterator
     * @return Filesystem
     */
    public function getFilesystem(File $fileIterator): Filesystem
    {
        $fileResolver = $this->createMock(Module::class);
        $fileResolver->method('get')
            ->willReturn($fileIterator);
        $validationState = $this->createMock(ValidationState::class);
        $validationState->method('isValidationRequired')
            ->willReturn(false);
        $filesystem = new Filesystem(
            $fileResolver,
            $this->createMock(ConverterInterface::class),
            $this->createMock(SchemaLocatorInterface::class),
            $validationState,
            ''
        );

        return $filesystem;
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        parent::tearDownAfterClass();
    }
}
