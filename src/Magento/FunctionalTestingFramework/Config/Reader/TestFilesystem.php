<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Config\Reader;

use Magento\FunctionalTestingFramework\Config\Dom\TestDom;

class TestFilesystem extends Filesystem
{
    /**
     * Method to redirect file name passing into TestDom class
     *
     * @param array $fileList
     * @return array
     * @throws \Exception
     */
    public function readFiles($fileList)
    {
        /** @var \Magento\FunctionalTestingFramework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->createConfigMerger(TestDom::class, $content, $fileList->getFilename());
                } else {
                    $configMerger->merge($content, $fileList->getFilename());
                }
            } catch (\Magento\FunctionalTestingFramework\Config\Dom\ValidationException $e) {
                throw new \Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->validationState->isValidationRequired()) {
            $errors = [];
            if ($configMerger && !$configMerger->validate($this->schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Exception($message . implode("\n", $errors));
            }
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
     * @param string $mergerClass
     * @param string $initialContents
     * @param string $filename
     * @return \Magento\FunctionalTestingFramework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function createConfigMerger($mergerClass, $initialContents, $filename = null)
    {
        $result = new $mergerClass(
            $initialContents,
            $filename,
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
