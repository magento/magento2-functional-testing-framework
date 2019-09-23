<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Config;

use Magento\FunctionalTestingFramework\ObjectManager\Config\Mapper\ArgumentParser;
use Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;

/**
 * Converter for configuration data.
 */
class Converter implements \Magento\FunctionalTestingFramework\Config\ConverterInterface
{
    /**
     * Unique identifier of node.
     */
    const NAME_ATTRIBUTE = 'name';

    /**
     * Argument parser.
     *
     * @var ArgumentParser
     */
    protected $argumentParser;

    /**
     * Argument interpreter.
     *
     * @var InterpreterInterface
     */
    protected $argumentInterpreter;

    /**
     * Argument node name.
     *
     * @var string
     */
    protected $argumentNodeName;

    /**
     * Id attributes.
     *
     * @var string[]
     */
    protected $idAttributes;

    /**
     * Constructor for Converter object.
     *
     * @param ArgumentParser       $argumentParser
     * @param InterpreterInterface $argumentInterpreter
     * @param string               $argumentNodeName
     * @param array                $idAttributes
     */
    public function __construct(
        ArgumentParser $argumentParser,
        InterpreterInterface $argumentInterpreter,
        $argumentNodeName,
        array $idAttributes = []
    ) {
        $this->argumentParser = $argumentParser;
        $this->argumentInterpreter = $argumentInterpreter;
        $this->argumentNodeName = $argumentNodeName;
        $this->idAttributes = $idAttributes;
    }

    /**
     * Convert XML to array.
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        return $this->convertXml($source->documentElement->childNodes);
    }

    /**
     * Convert XML node to array or string recursive.
     *
     * @param \DOMNodeList|array $elements
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @TODO ported magento code - to be refactored later
     */
    protected function convertXml($elements)
    {
        $result = [];

        foreach ($elements as $element) {
            if ($element instanceof \DOMElement) {
                if ($element->getAttribute('remove') == 'true') {
                    // Remove element
                    continue;
                }
                if ($element->hasAttribute('xsi:type')) {
                    if ($element->hasAttribute('path')) {
                        $elementData = $this->getAttributes($element);
                        $elementData['value'] = $this->argumentInterpreter->evaluate(
                            $this->argumentParser->parse($element)
                        );
                        unset($elementData['xsi:type'], $elementData['item']);
                    } else {
                        $elementData = $this->argumentInterpreter->evaluate(
                            $this->argumentParser->parse($element)
                        );
                    }
                } else {
                    $elementData = array_merge(
                        $this->getAttributes($element),
                        $this->getChildNodes($element)
                    );
                }
                $key = $this->getElementKey($element);
                if ($key) {
                    $result[$element->nodeName][$key] = $elementData;
                } elseif (!empty($elementData)) {
                    $result[$element->nodeName][] = $elementData;
                }
            } elseif ($element->nodeType == XML_TEXT_NODE && trim($element->nodeValue) != '') {
                return ['value' => $element->nodeValue];
            }
        }

        return $result;
    }

    /**
     * Get key for DOM element
     *
     * @param \DOMElement $element
     * @return boolean|string
     */
    protected function getElementKey(\DOMElement $element)
    {
        if (isset($this->idAttributes[$element->nodeName])) {
            if ($element->hasAttribute($this->idAttributes[$element->nodeName])) {
                return $element->getAttribute($this->idAttributes[$element->nodeName]);
            }
        }
        if ($element->hasAttribute(self::NAME_ATTRIBUTE)) {
            return $element->getAttribute(self::NAME_ATTRIBUTE);
        }
        return false;
    }

    /**
     * Verify attribute is main key for element.
     *
     * @param \DOMElement $element
     * @param \DOMAttr    $attribute
     * @return boolean
     */
    protected function isKeyAttribute(\DOMElement $element, \DOMAttr $attribute)
    {
        if (isset($this->idAttributes[$element->nodeName])) {
            return $attribute->name == $this->idAttributes[$element->nodeName];
        } else {
            return $attribute->name == self::NAME_ATTRIBUTE;
        }
    }

    /**
     * Get node attributes.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function getAttributes(\DOMElement $element)
    {
        $attributes = [];
        if ($element->hasAttributes()) {
            /** @var \DomAttr $attribute */
            foreach ($element->attributes as $attribute) {
                if (trim($attribute->nodeValue) != '' && !$this->isKeyAttribute($element, $attribute)) {
                    $attributes[$attribute->nodeName] = $this->castNumeric($attribute->nodeValue);
                }
            }
        }
        return $attributes;
    }

    /**
     * Get child nodes data.
     *
     * @param \DOMElement $element
     * @return array
     */
    protected function getChildNodes(\DOMElement $element)
    {
        $children = [];
        if ($element->hasChildNodes()) {
            $children = $this->convertXml($element->childNodes);
        }
        return $children;
    }

    /**
     * Cast nodeValue to int or double.
     *
     * @param string $nodeValue
     * @return float|integer
     */
    protected function castNumeric($nodeValue)
    {
        if (is_numeric($nodeValue)) {
            if (preg_match('/^\d+$/', $nodeValue)) {
                $nodeValue = (int) $nodeValue;
            } else {
                $nodeValue = (double) $nodeValue;
            }
        }

        return $nodeValue;
    }
}
