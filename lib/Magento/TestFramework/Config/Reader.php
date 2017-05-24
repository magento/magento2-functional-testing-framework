<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Config;

/**
 * Class Reader
 * Module declaration reader. Reads scenario.xml declaration files from module /etc directories.
 */
class Reader extends \Magento\TestFramework\Config\Reader\Filesystem
{
    /**
     * List of name attributes for merge.
     *
     * @var array
     */
    protected $_idAttributes = [
        '/scenarios/scenario' => 'name',
        '/scenarios/scenario/methods/method' => 'name',
        '/scenarios/scenario/methods/method/steps/step' => 'name',
    ];

    /**
     * @constructor
     * @param FileResolverInterface $fileResolver
     * @param ConverterInterface $converter
     * @param SchemaLocatorInterface $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param string $fileName [optional]
     * @param array $idAttributes [optional]
     * @param string $domDocumentClass [optional]
     * @param string $defaultScope [optional]
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        ConverterInterface $converter,
        SchemaLocatorInterface $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'scenario.xml',
        $idAttributes = [],
        $domDocumentClass = 'Magento\TestFramework\Config\Dom',
        $defaultScope = 'etc'
    ) {
        $this->_fileResolver = $fileResolver;
        $this->_converter = $converter;
        $this->_fileName = $fileName;
        $this->_idAttributes = array_replace($this->_idAttributes, $idAttributes);
        $this->_schemaFile = $schemaLocator->getSchema();
        $this->_isValidated = $validationState->isValidated();
        $this->_perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->_isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->_domDocumentClass = $domDocumentClass;
        $this->_defaultScope = $defaultScope;
    }
}
