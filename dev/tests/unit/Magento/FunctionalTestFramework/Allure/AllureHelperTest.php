<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Magento\FunctionalTestingFramework\Allure\Event\AddUniqueAttachmentEvent;
use Magento\FunctionalTestingFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yandex\Allure\Adapter\Allure;
use Yandex\Allure\Adapter\AllureException;
use Yandex\Allure\Adapter\Event\StepFinishedEvent;
use Yandex\Allure\Adapter\Event\StepStartedEvent;
use Yandex\Allure\Adapter\Model\Attachment;

class AllureHelperTest extends TestCase
{
    private const MOCK_FILENAME = 'filename';

    /**
     * The AddAttachmentToStep should add an attachment to the current step.
     *
     * @return void
     * @throws AllureException
     */
    public function testAddAttachmentToStep(): void
    {
        $expectedData = 'string';
        $expectedCaption = 'caption';
        $this->mockAttachmentWriteEvent($expectedData, $expectedCaption);

        //Prepare Allure lifecycle
        Allure::lifecycle()->fire(new StepStartedEvent('firstStep'));

        //Call function
        AllureHelper::addAttachmentToCurrentStep($expectedData, $expectedCaption);

        // Assert Attachment is created as expected
        $step = Allure::lifecycle()->getStepStorage()->pollLast();
        $expectedAttachment = new Attachment($expectedCaption, self::MOCK_FILENAME, null);
        $this->assertEquals($step->getAttachments()[0], $expectedAttachment);
    }

    /**
     * The AddAttachmentToLastStep should add an attachment only to the last step.
     *
     * @return void
     * @throws AllureException
     */
    public function testAddAttachmentToLastStep(): void
    {
        $expectedData = 'string';
        $expectedCaption = 'caption';
        $this->mockAttachmentWriteEvent($expectedData, $expectedCaption);

        //Prepare Allure lifecycle
        Allure::lifecycle()->fire(new StepStartedEvent('firstStep'));
        Allure::lifecycle()->fire(new StepFinishedEvent('firstStep'));
        Allure::lifecycle()->fire(new StepStartedEvent('secondStep'));
        Allure::lifecycle()->fire(new StepFinishedEvent('secondStep'));

        //Call function
        AllureHelper::addAttachmentToLastStep($expectedData, $expectedCaption);

        //Continue Allure lifecycle
        Allure::lifecycle()->fire(new StepStartedEvent('thirdStep'));
        Allure::lifecycle()->fire(new StepFinishedEvent('thirdStep'));

        // Assert Attachment is created as expected on the right step
        $rootStep = Allure::lifecycle()->getStepStorage()->pollLast();

        $firstStep = $rootStep->getSteps()[0];
        $secondStep = $rootStep->getSteps()[1];
        $thirdStep = $rootStep->getSteps()[2];

        $expectedAttachment = new Attachment($expectedCaption, self::MOCK_FILENAME, null);
        $this->assertEmpty($firstStep->getAttachments());
        $this->assertEquals($secondStep->getAttachments()[0], $expectedAttachment);
        $this->assertEmpty($thirdStep->getAttachments());
    }

    /**
     * The AddAttachment actions should have files with different attachment names.
     *
     * @return void
     * @throws AllureException
     */
    public function testAddAttachmentUniqueName(): void
    {
        $expectedData = 'string';
        $expectedCaption = 'caption';

        //Prepare Allure lifecycle
        Allure::lifecycle()->fire(new StepStartedEvent('firstStep'));

        //Call function twice
        AllureHelper::addAttachmentToCurrentStep($expectedData, $expectedCaption);
        AllureHelper::addAttachmentToCurrentStep($expectedData, $expectedCaption);

        // Assert file names for both attachments are not the same.
        $step = Allure::lifecycle()->getStepStorage()->pollLast();
        $attachmentOne = $step->getAttachments()[0]->getSource();
        $attachmentTwo = $step->getAttachments()[1]->getSource();
        $this->assertNotEquals($attachmentOne, $attachmentTwo);
    }

    /**
     * Clear Allure Lifecycle.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Allure::setDefaultLifecycle();

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null);
    }

    /**
     * Mock entire attachment writing mechanisms.
     *
     * @param string $filePathOrContents
     * @param string $caption
     *
     * @return void
     */
    private function mockAttachmentWriteEvent(string $filePathOrContents, string $caption): void
    {
        $mockInstance = $this->getMockBuilder(AddUniqueAttachmentEvent::class)
            ->setConstructorArgs([$filePathOrContents, $caption])
            ->disallowMockingUnknownTypes()
            ->onlyMethods(['getAttachmentFileName'])
            ->getMock();

        $mockInstance
            ->method('getAttachmentFileName')
            ->willReturn(self::MOCK_FILENAME);

        $objectManagerMockInstance = $this->createMock(ObjectManager::class);
        $objectManagerMockInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (string $class) use ($mockInstance) {
                        if ($class === AddUniqueAttachmentEvent::class) {
                            return $mockInstance;
                        }

                        return null;
                    }
                )
            );

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue($objectManagerMockInstance, $objectManagerMockInstance);
    }
}
