<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\Reader;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\Collector\ExceptionCollector;
use Magento\FunctionalTestingFramework\Util\Iterator\File;

class MftfFilesystem extends \Magento\FunctionalTestingFramework\Config\Reader\Filesystem
{
    /**
     * Method to redirect file name passing into Dom class
     *
     * @param File $fileList
     * @return array
     * @throws \Exception
     */
    public function readFiles($fileList)
    {
        $exceptionCollector = new ExceptionCollector();
        /** @var \Magento\FunctionalTestingFramework\Test\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            //check if file is empty and continue to next if it is
            if (!parent::verifyFileEmpty($content, $fileList->getFilename())) {
                continue;
            }
            try {
                if (!$configMerger) {
                    $configMerger = $this->createConfigMerger(
                        $this->domDocumentClass,
                        $content,
                        $fileList->getFilename(),
                        $exceptionCollector
                    );
                } else {
                    $configMerger->merge($content, $fileList->getFilename(), $exceptionCollector);
                }
                if (MftfApplicationConfig::getConfig()->debugEnabled()) {
                    $this->validateSchema($configMerger, $fileList->getFilename());
                }
            } catch (\Magento\FunctionalTestingFramework\Config\Dom\ValidationException $e) {
                throw new \Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        $exceptionCollector->throwException();
        if ($fileList->valid()) {
            $this->validateSchema($configMerger, $fileList->getFilename());
        }

        $output = [];
        if ($configMerger) {
            $output = $this->converter->convert($configMerger->getDom());
        }
        return $output;
    }

    /**
     * Return newly created instance of a config merger
     *
     * @param string             $mergerClass
     * @param string             $initialContents
     * @param string             $filename
     * @param ExceptionCollector $exceptionCollector
     * @return \Magento\FunctionalTestingFramework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function createConfigMerger($mergerClass, $initialContents, $filename = null, $exceptionCollector = null)
    {
        $result = new $mergerClass(
            $initialContents,
            $filename,
            $exceptionCollector,
            $this->idAttributes,
            null,
            $this->perFileSchema
        );
        if (!$result instanceof \Magento\FunctionalTestingFramework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}
