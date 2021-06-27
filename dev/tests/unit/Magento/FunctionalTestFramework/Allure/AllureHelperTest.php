<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Magento\FunctionalTestFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Magento\FunctionalTestingFramework\Allure\Event\AddUniqueAttachmentEvent;
use PHPUnit\Framework\TestCase;
use Yandex\Allure\Adapter\Allure;
use Yandex\Allure\Adapter\AllureException;
use Yandex\Allure\Adapter\Event\StepFinishedEvent;
use Yandex\Allure\Adapter\Event\StepStartedEvent;
use Yandex\Allure\Adapter\Model\Attachment;

class AllureHelperTest extends TestCase
{
    const MOCK_FILENAME = 'filename';

    /**
     * Clear Allure Lifecycle
     */
    public function tearDown(): void
    {
        Allure::setDefaultLifecycle();
    }

    /**
     * AddAttachmentToStep should add an attachment to the current step
     * @throws AllureException
     */
    public function testAddAttachmentToStep()
    {
        $this->mockAttachmentWriteEvent();
        $expectedData = "string";
        $expectedCaption = "caption";

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
     * AddAttachmentToLastStep should add an attachment only to the last step
     * @throws AllureException
     */
    public function testAddAttachmentToLastStep()
    {
        $this->mockAttachmentWriteEvent();
        $expectedData = "string";
        $expectedCaption = "caption";

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
     * AddAttachment actions should have files with different attachment names
     * @throws AllureException
     */
    public function testAddAttachementUniqueName()
    {
        $expectedData = "string";
        $expectedCaption = "caption";

        $this->mockCopyFile($expectedData, $expectedCaption);

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
     * Mock entire attachment writing mechanisms
     */
    public function mockAttachmentWriteEvent()
    {
        $this->createMock(AddUniqueAttachmentEvent::class)
            ->expects($this->any())
            ->method('getAttachmentFileName')
            ->willReturn(self::MOCK_FILENAME);
    }

    /**
     * Mock only file writing mechanism
     * @throws \ReflectionException
     */
    public function mockCopyFile(string $expectedData, string $expectedCaption)
    {
        $addUniqueAttachmentEvent = new AddUniqueAttachmentEvent($expectedData, $expectedCaption);
        $reflection = new \ReflectionClass(AddUniqueAttachmentEvent::class);
        $reflectionMethod = $reflection->getMethod('copyFile');
        $reflectionMethod->setAccessible(true);
        $output = $reflectionMethod->invoke($addUniqueAttachmentEvent);
        $this->assertEquals(true, $output);
    }
}
