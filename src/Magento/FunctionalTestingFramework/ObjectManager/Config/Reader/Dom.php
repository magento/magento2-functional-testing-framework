<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager\Config\Reader;

/**
 * Class Dom
 *
 * @internal
 */
// @codingStandardsIgnoreFile
class Dom extends \Magento\FunctionalTestingFramework\Config\Reader\Filesystem
{
    /**
     * Name of an attribute that stands for data type of node values
     */
    const TYPE_ATTRIBUTE = 'xsi:type';

    /**
     * Dom constructor.
     * @param \Magento\FunctionalTestingFramework\Config\FileResolverInterface $fileResolver
     * @param \Magento\FunctionalTestingFramework\ObjectManager\Config\Mapper\Dom $converter
     * @param \Magento\FunctionalTestingFramework\ObjectManager\Config\SchemaLocator $schemaLocator
     * @param \Magento\FunctionalTestingFramework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\FunctionalTestingFramework\Config\FileResolverInterface $fileResolver,
        \Magento\FunctionalTestingFramework\ObjectManager\Config\Mapper\Dom $converter,
        \Magento\FunctionalTestingFramework\ObjectManager\Config\SchemaLocator $schemaLocator,
        \Magento\FunctionalTestingFramework\Config\ValidationStateInterface $validationState,
        $fileName = 'di.xml',
        $idAttributes = [
            '/config/preference' => 'for',
            '/config/(type|virtualType)' => 'name',
            '/config/(type|virtualType)/arguments/argument' => 'name',
            '/config/(type|virtualType)/arguments/argument(/item)+' => 'name'
        ],
        $domDocumentClass = 'Magento\FunctionalTestingFramework\Config\Dom',
        $defaultScope = 'etc'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    /**
     * Create and return a config merger instance that takes into account types of arguments
     *
     * @param string $mergerClass
     * @param string $initialContents
     * @return \Magento\FunctionalTestingFramework\Config\Dom
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        return new $mergerClass($initialContents, $this->_idAttributes, self::TYPE_ATTRIBUTE, $this->_perFileSchema);
    }
}
