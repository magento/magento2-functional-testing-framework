<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\ObjectManager\Config\Mapper;

use Magento\TestFramework\Config\Converter\Dom\Flat as FlatConverter;
use Magento\TestFramework\Config\Dom\ArrayNodeConfig;
use Magento\TestFramework\Config\Dom\NodePathMatcher;

/**
 * Parser of a DI argument node that returns its array representation with no data loss
 */
class ArgumentParser
{
    /**
     * @var FlatConverter
     */
    private $converter;

    /**
     * Build and return array representation of DI argument node
     *
     * @param \DOMNode $argumentNode
     * @return array|string
     */
    public function parse(\DOMNode $argumentNode)
    {
        // Base path is specified to use more meaningful XPaths in config
        return $this->getConverter()->convert($argumentNode, 'argument');
    }

    /**
     * Retrieve instance of XML converter, suitable for DI argument nodes
     *
     * @return FlatConverter
     */
    protected function getConverter()
    {
        if (!$this->converter) {
            $arrayNodeConfig = new ArrayNodeConfig(new NodePathMatcher(), ['argument(/item)+' => 'name']);
            $this->converter = new FlatConverter($arrayNodeConfig);
        }
        return $this->converter;
    }
}
