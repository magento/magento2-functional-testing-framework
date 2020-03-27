<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Allure\Event;

use Symfony\Component\Mime\MimeTypes;
use Yandex\Allure\Adapter\AllureException;
use Yandex\Allure\Adapter\Event\AddAttachmentEvent;

const DEFAULT_FILE_EXTENSION = 'txt';
const DEFAULT_MIME_TYPE = 'text/plain';

class AddUniqueAttachmentEvent extends AddAttachmentEvent
{
    /**
     * @var string
     */
    private $type;

    /**
     * Near copy of parent function, added uniqid call for filename to prevent buggy allure behavior
     * @param string $filePathOrContents
     * @param string $type
     * @return string
     * @throws AllureException
     */
    public function getAttachmentFileName($filePathOrContents, $type)
    {
        $filePath = $filePathOrContents;
        if (!file_exists($filePath) || !is_file($filePath)) {
            //Save contents to temporary file
            $filePath = tempnam(sys_get_temp_dir(), 'allure-attachment');
            if (!file_put_contents($filePath, $filePathOrContents)) {
                throw new AllureException("Failed to save attachment contents to $filePath");
            }
        }

        if (!isset($type)) {
            $type = $this->guessFileMimeType($filePath);
            $this->type = $type;
        }

        $fileExtension = $this->guessFileExtension($type);

        $fileSha1 = uniqid(sha1_file($filePath));
        $outputPath = parent::getOutputPath($fileSha1, $fileExtension);
        if (!$this->copyFile($filePath, $outputPath)) {
            throw new AllureException("Failed to copy attachment from $filePath to $outputPath.");
        }

        return $this->getOutputFileName($fileSha1, $fileExtension);
    }

    /**
     * Copies file from one path to another. Wrapper for mocking in unit test.
     * @param string $filePath
     * @param string $outputPath
     * @return boolean
     */
    private function copyFile($filePath, $outputPath)
    {
        return copy($filePath, $outputPath);
    }

    /**
     * Copy of parent private function
     * @param string $filePath
     * @return string
     */
    private function guessFileMimeType($filePath)
    {
        $type = MimeTypes::getDefault()->guessMimeType($filePath);
        if (!isset($type)) {
            return DEFAULT_MIME_TYPE;
        }
        return $type;
    }

    /**
     * Copy of parent private function
     * @param string $mimeType
     * @return string
     */
    private function guessFileExtension($mimeType)
    {
        $candidate = MimeTypes::getDefault()->getExtensions($mimeType);
        if (empty($candidate)) {
            return DEFAULT_FILE_EXTENSION;
        }
        return reset($candidate);
    }

    /**
     * Copy of parent private function
     * @param string $sha1
     * @param string $extension
     * @return string
     */
    public function getOutputFileName($sha1, $extension)
    {
        return $sha1 . '-attachment.' . $extension;
    }
}
