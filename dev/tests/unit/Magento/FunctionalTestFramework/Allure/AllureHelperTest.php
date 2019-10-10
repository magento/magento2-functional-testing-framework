<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tests\unit\Magento\FunctionalTestingFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Yandex\Allure\Adapter\Allure;
use Yandex\Allure\Adapter\Event\AddAttachmentEvent;
use Yandex\Allure\Adapter\Event\StepFinishedEvent;
use Yandex\Allure\Adapter\Event\StepStartedEvent;
use Yandex\Allure\Adapter\Model\Attachment;
use AspectMock\Test as AspectMock;
use PHPUnit\Framework\TestCase;

class AllureHelperTest extends TestCase
{
    const MOCK_FILENAME = 'filename';

    /**
     * Clear Allure Lifecycle
     */
    public function tearDown()
    {
        Allure::setDefaultLifecycle();
    }

    /**
     * AddAtachmentToStep should add an attachment to the current step
     * @throws \Yandex\Allure\Adapter\AllureException
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
     * @throws \Yandex\Allure\Adapter\AllureException
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
     * Mock file system manipulation function
     * @throws \Exception
     */
    public function mockAttachmentWriteEvent()
    {
        AspectMock::double(AddAttachmentEvent::class, [
            "getAttachmentFileName" => self::MOCK_FILENAME
        ]);
    }
}
