<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
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
            $data = self::serializeAttachmentData($data);
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
            $data = self::serializeAttachmentData($data);
        }
        if (@file_exists($data) && is_file($data)) {
            Allure::attachmentFile($caption, $data);
        } else {
            Allure::attachment($caption, $data);
        }
    }

    /**
     * Serializes attachment data for the Allure report, falling back to a descriptive placeholder
     * instead of letting an unserializable value (e.g. one containing a Closure) abort the whole
     * test run: a failure to attach a debugging artifact must not be fatal to the test suite.
     *
     * @param mixed $data
     * @return string
     */
    private static function serializeAttachmentData($data): string
    {
        try {
            return serialize($data);
        } catch (\Exception $exception) {
            return sprintf(
                'Unable to serialize attachment data of type "%s": %s',
                is_object($data) ? get_class($data) : gettype($data),
                $exception->getMessage()
            );
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
