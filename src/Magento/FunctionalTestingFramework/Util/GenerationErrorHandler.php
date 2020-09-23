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
                $totalErrors = count($this->getErrorsByType($type));
                $totalAnnotationErrors = 0;
                foreach ($this->getErrorsByType($type) as $entity => $error) {
                    if ( (is_array($error['generated']) && $error['generated'][0] === true)
                        || ($error['generated'] === true) ) {
                        $totalAnnotationErrors++;
                    }
                }
                $totalNotGenErrors = $totalErrors - $totalAnnotationErrors;
                if ($totalNotGenErrors > 0) {
                    print(
                        PHP_EOL
                        . 'ERROR: '
                        . strval($totalNotGenErrors)
                        . ' '
                        . ucfirst($type)
                        . "(s) failed to generate. See mftf.log for details."
                    );
                }
                if ($totalAnnotationErrors > 0) {
                    print(
                        PHP_EOL
                        . 'ERROR: '
                        . strval($totalAnnotationErrors)
                        . ' '
                        . ucfirst($type)
                        . "(s) generated with annotation errors. See mftf.log for details."
                    );
                }
            }
            print(PHP_EOL);
        }
    }
}
