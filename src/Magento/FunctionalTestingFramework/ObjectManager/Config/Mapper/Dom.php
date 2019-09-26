<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager\Config\Mapper;

use Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;
use Magento\FunctionalTestingFramework\Stdlib\BooleanUtils;

// @codingStandardsIgnoreFile
class Dom implements \Magento\FunctionalTestingFramework\Config\ConverterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var ArgumentParser
     */
    private $argumentParser;

    /**
     * @var InterpreterInterface
     */
    private $argumentInterpreter;

    /**
     * @param BooleanUtils $booleanUtils
     * @param ArgumentParser $argumentParser
     * @param InterpreterInterface $argumentInterpreter
     */
    public function __construct(
        InterpreterInterface $argumentInterpreter,
        BooleanUtils $booleanUtils = null,
        ArgumentParser $argumentParser = null
    ) {
        $this->argumentInterpreter = $argumentInterpreter;
        $this->booleanUtils = $booleanUtils ?: new BooleanUtils();
        $this->argumentParser = $argumentParser ?: new ArgumentParser();
    }

    /**
     * Convert configuration in DOM format to assoc array that can be used by object manager
     *
     * @param \DOMDocument $config
     * @return array
     * @throws \Exception
     * @todo this method has high cyclomatic complexity in order to avoid performance issues
     */
    public function convert($config)
    {
        $output = [];
        /** @var \DOMNode $node */
        foreach ($config->documentElement->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            switch ($node->nodeName) {
                case 'preference':
                    $output['preferences'][$node->attributes->getNamedItem(
                        'for'
                    )->nodeValue] = $node->attributes->getNamedItem(
                        'type'
                    )->nodeValue;
                    break;
                case 'type':
                case 'virtualType':
                    $typeData = [];
                    $typeNodeAttributes = $node->attributes;
                    $typeNodeShared = $typeNodeAttributes->getNamedItem('shared');
                    if ($typeNodeShared) {
                        $typeData['shared'] = $this->booleanUtils->toBoolean($typeNodeShared->nodeValue);
                    }
                    if ($node->nodeName == 'virtualType') {
                        $attributeType = $typeNodeAttributes->getNamedItem('type');
                        // attribute type is required for virtual type only in merged configuration
                        if ($attributeType) {
                            $typeData['type'] = $attributeType->nodeValue;
                        }
                    }
                    $typeData['arguments'] = $this->setTypeArguments($node);
                    $output[$typeNodeAttributes->getNamedItem('name')->nodeValue] = $typeData;
                    break;
                default:
                    throw new \Exception("Invalid application config. Unknown node: {$node->nodeName}.");
            }
        }

        return $output;
    }

    /** Read typeChildNodes and set typeArguments
     * @param DOMNode $node
     * @return mixed
     * @throws \Exception
     */
    private function setTypeArguments($node)
    {
        $typeArguments = [];

        foreach ($node->childNodes as $typeChildNode) {
            /** @var \DOMNode $typeChildNode */
            if ($typeChildNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            switch ($typeChildNode->nodeName) {
                case 'arguments':
                    /** @var \DOMNode $argumentNode */
                    foreach ($typeChildNode->childNodes as $argumentNode) {
                        if ($argumentNode->nodeType != XML_ELEMENT_NODE) {
                            continue;
                        }
                        $argumentName = $argumentNode->attributes->getNamedItem('name')->nodeValue;
                        $argumentData = $this->argumentParser->parse($argumentNode);
                        $typeArguments[$argumentName] = $this->argumentInterpreter->evaluate(
                            $argumentData
                        );
                    }
                    break;

                default:
                    throw new \Exception(
                        "Invalid application config. Unknown node: {$typeChildNode->nodeName}."
                    );
            }
        }
        return $typeArguments;
    }
}
