<?php
/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magento\AcceptanceTestFramework\Config\Reader;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Filesystem implements \Magento\AcceptanceTestFramework\Config\ReaderInterface
{
    /**
     * File locator
     *
     * @var \Magento\AcceptanceTestFramework\Config\FileResolverInterface
     */
    protected $_fileResolver;

    /**
     * Config converter
     *
     * @var \Magento\AcceptanceTestFramework\Config\ConverterInterface
     */
    protected $_converter;

    /**
     * The name of file that stores configuration
     *
     * @var string
     */
    protected $_fileName;

    /**
     * Path to corresponding XSD file with validation rules for merged config
     *
     * @var string
     */
    protected $_schema;

    /**
     * Path to corresponding XSD file with validation rules for separate config files
     *
     * @var string
     */
    protected $_perFileSchema;

    /**
     * List of id attributes for merge
     *
     * @var array
     */
    protected $_idAttributes = [];

    /**
     * Class of dom configuration document used for merge
     *
     * @var string
     */
    protected $_domDocumentClass;

    /**
     * @var \Magento\AcceptanceTestFramework\Config\ValidationStateInterface
     */
    protected $validationState;

    /**
     * @var string
     */
    protected $_defaultScope;

    /**
     * @var string
     */
    protected $_schemaFile;

    /**
     * Constructor
     *
     * @param \Magento\AcceptanceTestFramework\Config\FileResolverInterface $fileResolver
     * @param \Magento\AcceptanceTestFramework\Config\ConverterInterface $converter
     * @param \Magento\AcceptanceTestFramework\Config\SchemaLocatorInterface $schemaLocator
     * @param \Magento\AcceptanceTestFramework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\AcceptanceTestFramework\Config\FileResolverInterface $fileResolver,
        \Magento\AcceptanceTestFramework\Config\ConverterInterface $converter,
        \Magento\AcceptanceTestFramework\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\AcceptanceTestFramework\Config\ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = [],
        $domDocumentClass = \Magento\AcceptanceTestFramework\Config\Dom::class,
        $defaultScope = 'global'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->validationState = $validationState;
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() && $validationState->isValidationRequired()
            ? $schemaLocator->getPerFileSchema() : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
    }

    /**
     * Load configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $scope = $scope ?: $this->_defaultScope;
        $fileList = $this->_fileResolver->get($this->_fileName, $scope);
        if (!count($fileList)) {
            return [];
        }
        $output = $this->_readFiles($fileList);

        return $output;
    }

    /**
     * Read configuration files
     *
     * @param array $fileList
     * @return array
     * @throws \Exception
     */
    protected function _readFiles($fileList)
    {
        /** @var \Magento\AcceptanceTestFramework\Config\Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                if (!$configMerger) {
                    $configMerger = $this->_createConfigMerger($this->_domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (\Magento\AcceptanceTestFramework\Config\Dom\ValidationException $e) {
                throw new \Exception("Invalid XML in file " . $key . ":\n" . $e->getMessage());
            }
        }
        if ($this->validationState->isValidationRequired()) {
            $errors = [];
            if ($configMerger && !$configMerger->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new \Exception($message . implode("\n", $errors));
            }
        }

        $output = [];
        if ($configMerger) {
            $output = $this->_converter->convert($configMerger->getDom());
        }
        return $output;
    }

    /**
     * Return newly created instance of a config merger
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\AcceptanceTestFramework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass(
            $initialContents,
            $this->_idAttributes,
            null,
            $this->_perFileSchema
        );
        if (!$result instanceof \Magento\AcceptanceTestFramework\Config\Dom) {
            throw new \UnexpectedValueException(
                "Instance of the DOM config merger is expected, got {$mergerClass} instead."
            );
        }
        return $result;
    }
}
