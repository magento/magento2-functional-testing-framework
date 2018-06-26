<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Config\Reader;

/**
 * Filesystem configuration loader. Loads configuration from XML files, split by scopes.
 */
class Filesystem extends \Magento\FunctionalTestingFramework\Config\Reader\Filesystem
{
    /**
     * An array of paths which do have a key for merging but instead are value based nodes which can only be appended
     *
     * @var array
     */
    private $mergeablePaths;

    /**
     * Constructor
     *
     * @param \Magento\FunctionalTestingFramework\Config\FileResolverInterface    $fileResolver
     * @param \Magento\FunctionalTestingFramework\Config\ConverterInterface       $converter
     * @param \Magento\FunctionalTestingFramework\Config\SchemaLocatorInterface   $schemaLocator
     * @param \Magento\FunctionalTestingFramework\Config\ValidationStateInterface $validationState
     * @param string                                                              $fileName
     * @param array                                                               $idAttributes
     * @param array                                                               $mergeablePaths
     * @param string                                                              $domDocumentClass
     * @param string                                                              $defaultScope
     */
    public function __construct(
        \Magento\FunctionalTestingFramework\Config\FileResolverInterface $fileResolver,
        \Magento\FunctionalTestingFramework\Config\ConverterInterface $converter,
        \Magento\FunctionalTestingFramework\Config\SchemaLocatorInterface $schemaLocator,
        \Magento\FunctionalTestingFramework\Config\ValidationStateInterface $validationState,
        $fileName,
        $idAttributes = [],
        $mergeablePaths = [],
        $domDocumentClass = \Magento\FunctionalTestingFramework\Config\Dom::class,
        $defaultScope = 'global'
    ) {
        $this->fileResolver = $fileResolver;
        $this->converter = $converter;
        $this->fileName = $fileName;
        $this->idAttributes = array_replace($this->idAttributes, $idAttributes);
        $this->mergeablePaths = $mergeablePaths;
        $this->validationState = $validationState;
        $this->schemaFile = $schemaLocator->getSchema();
        $this->perFileSchema = $schemaLocator->getPerFileSchema() && $validationState->isValidationRequired()
            ? $schemaLocator->getPerFileSchema() : null;
        $this->domDocumentClass = $domDocumentClass;
        $this->defaultScope = $defaultScope;
    }

    /**
     * Return newly created instance of a config merger. Overridden to include new arg in mergerClass.
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\FunctionalTestingFramework\Config\Dom
     * @throws \UnexpectedValueException
     */
    protected function createConfigMerger($mergerClass, $initialContents)
    {
        $result = new $mergerClass(
            $initialContents,
            $this->idAttributes,
            $this->mergeablePaths,
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
