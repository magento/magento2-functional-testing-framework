<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure;

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
        Allure::lifecycle()->fire(new AddAttachmentEvent($data, $caption));
    }

    /**
     * Adds Attachment to the last executed step. Required due to Allure root-step behavior
     * @param mixed  $data
     * @param string $caption
     * @return void
     */
    public static function addAttachmentToLastStep($data, $caption)
    {
        $rootStep = Allure::lifecycle()->getStepStorage()->getLast();
        $trueLastStep = array_last($rootStep->getSteps());

        $attachmentEvent = new AddAttachmentEvent($data, $caption);
        $attachmentEvent->process($trueLastStep);
    }
}
