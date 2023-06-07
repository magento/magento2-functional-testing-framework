<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Allure;

use Qameta\Allure\Allure;
use Qameta\Allure\Io\DataSourceInterface;

class AllureHelper
{
    /**
     * Adds attachment to the current step.
     *
     * @param mixed  $data
     * @param string $caption
     *
     * @return void
     */
    public static function addAttachmentToCurrentStep($data, $caption): void
    {
        if (!is_string($data)) {
            try {
                $data = serialize($data);
            } catch (\Exception $exception) {
                throw  new \Exception($data->getMessage());
            }
        }
        if (@file_exists($data) && is_file($data)) {
            Allure::attachmentFile($caption, $data);
        } else {
            Allure::attachment($caption, $data);
        }
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
        if (!is_string($data)) {
            $data = serialize($data);
        }
        if (@file_exists($data) && is_file($data)) {
            Allure::attachmentFile($caption, $data);
        } else {
            Allure::attachment($caption, $data);
        }
    }

    /**
     * @param DataSourceInterface $dataSource
     * @param string              $name
     * @param string|null         $type
     * @param string|null         $fileExtension
     * @return void
     */
    public static function doAddAttachment(
        DataSourceInterface $dataSource,
        string $name,
        ?string $type = null,
        ?string $fileExtension = null,
    ): void {
        $attachment = Allure::getConfig()
            ->getResultFactory()
            ->createAttachment()
            ->setName($name)
            ->setType($type)
            ->setFileExtension($fileExtension);
        Allure::getLifecycle()->addAttachment($attachment, $dataSource);
    }
}
