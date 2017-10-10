<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Magento\FunctionalTestingFramework\XmlParser;

/**
 * Interface for retrieving parser data.
 */
interface ParserInterface
{
    /**
     * Get parsed xml data.
     *
     * @param string $type
     * @return array
     */
    public function getData($type);
}
