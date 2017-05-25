<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ObjectManager\Config\Reader;

/**
 * Class Dom
 *
 * @internal
 */
class Dom extends \Magento\TestFramework\Config\Reader\Filesystem
{
    /**
     * Name of an attribute that stands for data type of node values
     */
    const TYPE_ATTRIBUTE = 'xsi:type';

    /**
     * @param \Magento\TestFramework\Config\FileResolverInterface $fileResolver
     * @param \Magento\TestFramework\ObjectManager\Config\Mapper\Dom $converter
     * @param \Magento\TestFramework\ObjectManager\Config\SchemaLocator $schemaLocator
     * @param \Magento\TestFramework\Config\ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        \Magento\TestFramework\Config\FileResolverInterface $fileResolver,
        \Magento\TestFramework\ObjectManager\Config\Mapper\Dom $converter,
        \Magento\TestFramework\ObjectManager\Config\SchemaLocator $schemaLocator,
        \Magento\TestFramework\Config\ValidationStateInterface $validationState,
        $fileName = 'di.xml',
        $idAttributes = [
            '/config/preference' => 'for',
            '/config/(type|virtualType)' => 'name',
            '/config/(type|virtualType)/arguments/argument' => 'name',
            '/config/(type|virtualType)/arguments/argument(/item)+' => 'name'
        ],
        $domDocumentClass = 'Magento\TestFramework\Config\Dom',
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
     * @return \Magento\TestFramework\Config\Dom
     */
    protected function _createConfigMerger($mergerClass, $initialContents)
    {
        return new $mergerClass($initialContents, $this->_idAttributes, self::TYPE_ATTRIBUTE, $this->_perFileSchema);
    }
}
