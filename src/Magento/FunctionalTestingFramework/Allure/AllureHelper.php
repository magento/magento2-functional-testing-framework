<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\Event\AddUniqueAttachmentEvent;
use Yandex\Allure\Adapter\Allure;
use Yandex\Allure\Adapter\Event\AddAttachmentEvent;

class AllureHelper
{
    /**
     * Adds attachment to the current step
     * @param mixed  $data
     * @param string $caption
     * @throws \Yandex\Allure\Adapter\AllureException
     * @return void
     */
    public static function addAttachmentToCurrentStep($data, $caption)
    {
        Allure::lifecycle()->fire(new AddUniqueAttachmentEvent($data, $caption));
    }

    /**
     * Adds Attachment to the last executed step.
     * Use this when adding attachments outside of an $I->doSomething() step/context.
     * @param mixed  $data
     * @param string $caption
     * @return void
     */
    public static function addAttachmentToLastStep($data, $caption)
    {
        $rootStep = Allure::lifecycle()->getStepStorage()->getLast();
        $trueLastStep = array_last($rootStep->getSteps());

        if ($trueLastStep == null) {
            // Nothing to attach to; do not fire off allure event
            return;
        }
        
        $attachmentEvent = new AddUniqueAttachmentEvent($data, $caption);
        $attachmentEvent->process($trueLastStep);
    }
}
