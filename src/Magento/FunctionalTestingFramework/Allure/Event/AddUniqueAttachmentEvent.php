<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Allure\Event;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Mime\MimeTypes;
use Yandex\Allure\Adapter\AllureException;
use Yandex\Allure\Adapter\Event\AddAttachmentEvent;

class AddUniqueAttachmentEvent extends AddAttachmentEvent
{
    private const DEFAULT_FILE_EXTENSION = 'txt';
    private const DEFAULT_MIME_TYPE = 'text/plain';

    /**
     * Near copy of parent function, added uniqid call for filename to prevent buggy allure behavior.
     *
     * @param mixed  $filePathOrContents
     * @param string $type
     *
     * @return string
     * @throws AllureException
     */
    public function getAttachmentFileName($filePathOrContents, $type): string
    {
        $filePath = $filePathOrContents;

        if (!is_string($filePath) || !file_exists($filePath) || !is_file($filePath)) {
            //Save contents to temporary file
            $filePath = tempnam(sys_get_temp_dir(), 'allure-attachment');
            if (!file_put_contents($filePath, $filePathOrContents)) {
                throw new AllureException("Failed to save attachment contents to $filePath");
            }
        }

        if (!isset($type)) {
            $type = $this->guessFileMimeType($filePath);
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
     *
     * @param string $filePath
     * @param string $outputPath
     *
     * @return boolean
     * @throws TestFrameworkException
     */
    private function copyFile(string $filePath, string $outputPath): bool
    {
        if (MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::UNIT_TEST_PHASE) {
            return true;
        }
        return copy($filePath, $outputPath);
    }

    /**
     * Copy of parent private function.
     *
     * @param string $filePath
     *
     * @return string
     */
    private function guessFileMimeType(string $filePath): string
    {
        $type = MimeTypes::getDefault()->guessMimeType($filePath);

        if (!isset($type)) {
            return self::DEFAULT_MIME_TYPE;
        }
        return $type;
    }

    /**
     * Copy of parent private function.
     *
     * @param string $mimeType
     *
     * @return string
     */
    private function guessFileExtension(string $mimeType): string
    {
        $candidate = MimeTypes::getDefault()->getExtensions($mimeType);

        if (empty($candidate)) {
            return self::DEFAULT_FILE_EXTENSION;
        }
        return reset($candidate);
    }
}
