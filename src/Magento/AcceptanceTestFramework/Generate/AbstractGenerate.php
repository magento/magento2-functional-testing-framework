<?php
namespace Magento\AcceptanceTestFramework\Generate;

use Magento\AcceptanceTestFramework\Config\DataInterface;
use Magento\AcceptanceTestFramework\ObjectManagerInterface;

/**
 * Abstract Generate class for all test entities.
 */
abstract class AbstractGenerate implements LauncherInterface
{
    /**
     * Counter.
     *
     * @var int
     */
    protected $cnt = 0;

    /**
     * Execution start in Unix timestamp with microseconds.
     *
     * @var int
     */
    protected $start;

    /**
     * Execution end in Unix timestamp with microseconds.
     *
     * @var int
     */
    protected $end;

    /**
     * An array of errors.
     *
     * @var string[]
     */
    protected $errors = [];

    /**
     * Object manager instance.
     *
     * @var \Magento\AcceptanceTestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * Configuration data instance.
     *
     * @var DataInterface
     */
    protected $configData;

    /**
     * @constructor
     * @param ObjectManagerInterface $objectManager
     * @param DataInterface $configData
     */
    public function __construct(ObjectManagerInterface $objectManager, DataInterface $configData)
    {
        $this->objectManager = $objectManager;
        $this->configData = $configData;
    }

    /**
     * Generate single class.
     *
     * @param string $className
     * @return string|bool
     */
    abstract public function generate($className);

    /**
     * Convert class name to camel-case.
     *
     * @param string $class
     * @return string
     */
    protected function toCamelCase($class)
    {
        $class = str_replace('_', ' ', $class);
        $class = str_replace('\\', ' ', $class);
        $class = str_replace('/', ' ', $class);

        return str_replace(' ', '', ucwords($class));
    }

    /**
     * Prepare data for phpdoc attribute "copyright".
     *
     * @return string
     */
    protected function getCopyright()
    {
        return 'Copyright © 2017 Magento. All rights reserved.';
    }

    /**
     * Prepare data for phpdoc attribute "license".
     *
     * @return string
     */
    protected function getLicenseNote()
    {
        return 'See COPYING.txt for license details.';
    }

    /**
     * Get file phpdoc with license and copyright information.
     *
     * @return string
     */
    protected function getFilePhpDoc()
    {
        $content = "/**\n";
        $content .= " * " . $this->getCopyright() . "\n";
        $content .= " * " . $this->getLicenseNote() . "\n";
        $content .= " */\n\n";
        return $content;
    }

    /**
     * Add error message.
     *
     * @param string $message
     * @return void
     */
    protected function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Get list of occurred errors.
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get short class name based on full class name.
     *
     * @param string $class
     * @return string
     */
    protected function getShortClassName($class)
    {
        $classNameArray = explode('\\', $class);
        return end($classNameArray);
    }

    /**
     * Get namespace class name based on full class name.
     *
     * @param string $class
     * @return string
     */
    protected function getNamespace($class)
    {
        $classNameArray = explode('\\', $class);
        return implode("\\", array_slice($classNameArray, 0, -1));
    }

    /**
     * Create class with specified content.
     *
     * @param string $class
     * @param string $content
     * @return string
     */
    protected function createClass($class, $content)
    {
        $fileName = $this->getShortClassName($class) . '.php';
        $relativeFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        $relativeFolderPath = str_replace(DIRECTORY_SEPARATOR . $fileName, '', $relativeFilePath);

        $filePath = TESTS_BP . '/generated/' . $relativeFilePath;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $folderPath = TESTS_BP . '/generated/' . $relativeFolderPath;
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $result = @file_put_contents($filePath, $content);

        if ($result === false) {
            $error = error_get_last();
            $this->addError(sprintf('Unable to generate %s class. Error: %s', $class, $error['message']));
            return false;
        }

        $this->cnt++;

        return $filePath;
    }
}
