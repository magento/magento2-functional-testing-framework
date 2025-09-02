<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
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
