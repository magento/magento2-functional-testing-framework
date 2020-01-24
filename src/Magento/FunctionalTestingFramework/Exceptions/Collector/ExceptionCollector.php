<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Exceptions\Collector;

class ExceptionCollector
{
    /**
     * Private array containing all errors to be thrown as part of the exception.
     *
     * @var array
     */
    private $errors = [];

    /**
     * Function to add a filename and message for the filename
     *
     * @param string $filename
     * @param string $message
     * @return void
     */
    public function addError($filename, $message)
    {
        $error[$filename] = $message;
        $this->errors = array_merge_recursive($this->errors, $error);
    }

    /**
     * Function which throws an exception when there are errors present.
     *
     * @return void
     * @throws \Exception
     */
    public function throwException()
    {
        if (empty($this->errors)) {
            return;
        }

        $errorMsg = implode("\n\n", $this->formatErrors($this->errors));
        throw new \Exception("\n" . $errorMsg);
    }

    /**
     * Return all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors ?? [];
    }

    /**
     * Reset error to empty array
     *
     * @return void
     */
    public function reset()
    {
        $this->errors = [];
    }

    /**
     * If there are multiple exceptions for a single file, the function flattens the array so they can be printed
     * as separate messages.
     *
     * @param array $errors
     * @return array
     */
    private function formatErrors($errors)
    {
        $flattenedErrors = [];
        foreach ($errors as $errorMsg) {
            if (is_array($errorMsg)) {
                $flattenedErrors = array_merge($flattenedErrors, $this->formatErrors($errorMsg));
                continue;
            }

            $flattenedErrors[] = $errorMsg;
        }

        return $flattenedErrors;
    }
}
