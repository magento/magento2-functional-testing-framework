<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use PHP_CodeSniffer\Exceptions\RuntimeException;

class GenerationErrorHandler
{
    /**
     * Generation Error Handler Instance
     *
     * @var GenerationErrorHandler
     */
    private static $instance;

    /**
     * Collected errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * Singleton method to return GenerationErrorHandler
     *
     * @return GenerationErrorHandler
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new GenerationErrorHandler();
        }

        return self::$instance;
    }

    /**
     * GenerationErrorHandler constructor
     */
    private function __construct()
    {
    }

    /**
     * Add a generation error into error handler
     *
     * @param string  $type
     * @param string  $entityName
     * @param string  $message
     * @param boolean $generated
     * @return void
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    public function addError($type, $entityName, $message, $generated = false)
    {
        if (MftfApplicationConfig::getConfig()->getPhase() != MftfApplicationConfig::EXECUTION_PHASE) {
            $error[$entityName] = [
                'message' => $message,
                'generated' => $generated,
            ];
            if (isset($this->errors[$type])) {
                $this->errors[$type] = array_merge_recursive($this->errors[$type], $error);
            } else {
                $this->errors[$type] = $error;
            }
        }
    }

    /**
     * Return all errors
     *
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errors;
    }

    /**
     * Return errors for given type
     *
     * @param string $type
     * @return array
     */
    public function getErrorsByType($type)
    {
        return $this->errors[$type] ?? [];
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
     * Print error summary in console
     *
     * @return void
     */
    public function printErrorSummary()
    {
        if (is_array(array_keys($this->errors))) {
            foreach (array_keys($this->errors) as $type) {
                print(
                    PHP_EOL
                    . 'ERROR: '
                    . strval(count($this->getErrorsByType($type)))
                    . ' '
                    . ucfirst($type)
                    . " failed to generate or generated but with annotation errors"
                );
            }
            print(PHP_EOL);
        }
    }
}
