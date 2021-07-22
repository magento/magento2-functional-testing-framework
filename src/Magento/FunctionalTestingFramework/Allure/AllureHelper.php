<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Allure;

use Magento\FunctionalTestingFramework\Allure\Event\AddUniqueAttachmentEvent;
use Yandex\Allure\Adapter\Allure;
use Yandex\Allure\Adapter\AllureException;

class AllureHelper
{
    /**
     * Adds attachment to the current step.
     *
     * @param mixed  $data
     * @param string $caption
     *
     * @return void
     * @throws AllureException
     */
    public static function addAttachmentToCurrentStep($data, $caption): void
    {
        Allure::lifecycle()->fire(AddUniqueAttachmentEvent::getInstance($data, $caption));
    }

    /**
     * Adds Attachment to the last executed step.
     * Use this when adding attachments outside of an $I->doSomething() step/context.
     *
     * @param mixed  $data
     * @param string $caption
     *
     * @return void
     */
    public static function addAttachmentToLastStep($data, $caption): void
    {
        $rootStep = Allure::lifecycle()->getStepStorage()->getLast();
        $trueLastStep = array_last($rootStep->getSteps());

        if ($trueLastStep == null) {
            // Nothing to attach to; do not fire off allure event
            return;
        }
        
        $attachmentEvent = AddUniqueAttachmentEvent::getInstance($data, $caption);
        $attachmentEvent->process($trueLastStep);
    }
}
