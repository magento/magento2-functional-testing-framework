<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Config;

/**
 * Class Reader
 * Module declaration reader. Reads scenario.xml declaration files from module /etc directories.
 */
class Reader extends \Magento\FunctionalTestingFramework\Config\Reader\Filesystem
{
    /**
     * List of name attributes for merge.
     *
     * @var array
     */
    protected $idAttributes = [
        '/scenarios/scenario' => 'name',
        '/scenarios/scenario/methods/method' => 'name',
        '/scenarios/scenario/methods/method/steps/step' => 'name',
    ];

    /**
     * Reader constructor.
     * @param FileResolverInterface    $fileResolver
     * @param ConverterInterface       $converter
     * @param SchemaLocatorInterface   $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param string                   $fileName
     * @param array                    $idAttributes
     * @param string                   $domDocumentClass
     * @param string                   $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        ConverterInterface $converter,
        SchemaLocatorInterface $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'scenario.xml',
        $idAttributes = [],
        $domDocumentClass = Magento\FunctionalTestingFramework\Config\Dom::class,
        $defaultScope = 'etc'
    ) {
        $this->fileResolver = $fileResolver;
        $this->converter = $converter;
        $this->fileName = $fileName;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
        $this->schemaFile = $schemaLocator->getSchema();
        $this->isValidated = $validationState->isValidated();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() &&
        $this->isValidated ? $schemaLocator->getPerFileSchema() : null;
        $this->domDocumentClass = $domDocumentClass;
        $this->defaultScope = $defaultScope;
    }
}
