<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use PHPUnit\Framework\TestCase;
use Qameta\Allure\Allure;
use Qameta\Allure\Io\DataSourceFactory;
use Qameta\Allure\Model\AttachmentResult;
use Qameta\Allure\Model\ResultFactoryInterface;
use Qameta\Allure\Setup\LifecycleBuilderInterface;
use const STDOUT;

/**
 * @covers \Qameta\Allure\Allure
 */
class AllureHelperTest extends TestCase
{
    public function setUp(): void
    {
        Allure::reset();
    }

    /**
     * @dataProvider providerAttachmentProperties
     */
    public function testDoAddAttachmentMethod(
        string $name,
        $type,
        ?string $fileExtension,
    ): void {
        $attachment = new AttachmentResult('a');
        Allure::setLifecycleBuilder(
            $this->createLifecycleBuilder($this->createResultFactoryWithAttachment($attachment)),
        );

        AllureHelper::doAddAttachment(
            DataSourceFactory::fromFile('test'),
            'nameOfTheFile',
            'typeOfTheFile',
            $fileExtension
        );
        self::assertSame('nameOfTheFile', $attachment->getName());
        self::assertSame('typeOfTheFile', $attachment->getType());
    }

    /**
     * @dataProvider providerAttachmentProperties
     */
    public function testAddAttachmentToStep(
        string $name,
        ?string $type,
        ?string $fileExtension,
    ): void {
        $attachment = new AttachmentResult('a');
        Allure::setLifecycleBuilder(
            $this->createLifecycleBuilder($this->createResultFactoryWithAttachment($attachment)),
        );

        Allure::attachment($name, 'nameOfTheFile', $type, $fileExtension);
        self::assertSame($name, $attachment->getName());
        self::assertSame($type, $attachment->getType());
        self::assertSame($fileExtension, $attachment->getFileExtension());
    }

    /**
     * @dataProvider providerAttachmentProperties
     */
    public function testAddAttachmentFileToStep(
        string $name,
        ?string $type,
        ?string $fileExtension,
    ): void {
        $attachment = new AttachmentResult('a');
        Allure::setLifecycleBuilder(
            $this->createLifecycleBuilder($this->createResultFactoryWithAttachment($attachment)),
        );

        Allure::attachmentFile($name, 'b', $type, '.html');
        self::assertSame('c', $attachment->getName());
        self::assertSame('.html', $attachment->getFileExtension());
    }

    /**
     * @return iterable<string, array{string, string|null, string|null}>
     */
    public static function providerAttachmentProperties(): iterable
    {
        return [
            'Only name' => ['c', null, null],
            'Name and type' => ['c', 'd', null],
            'Name and file extension' => ['c', null, 'd'],
            'Name, type and file extension' => ['c', 'd', 'e'],
        ];
    }

    private function createResultFactoryWithAttachment(AttachmentResult $attachment): ResultFactoryInterface
    {
        $resultFactory = $this->createStub(ResultFactoryInterface::class);
        $resultFactory
            ->method('createAttachment')
            ->willReturn($attachment);

        return $resultFactory;
    }

    private function createLifecycleBuilder(
        ?ResultFactoryInterface $resultFactory = null,
        ?AllureLifecycleInterface $lifecycle = null,
        ?StatusDetectorInterface $statusDetector = null,
    ): LifecycleBuilderInterface {
        $builder = $this->createStub(LifecycleBuilderInterface::class);
        if (isset($resultFactory)) {
            $builder
                ->method('getResultFactory')
                ->willReturn($resultFactory);
        }
        if (isset($lifecycle)) {
            $builder
                ->method('createLifecycle')
                ->willReturn($lifecycle);
        }
        if (isset($statusDetector)) {
            $builder
                ->method('getStatusDetector')
                ->willReturn($statusDetector);
        }

        return $builder;
    }
}
