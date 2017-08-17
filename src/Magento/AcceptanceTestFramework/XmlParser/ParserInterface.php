<?php
namespace Magento\AcceptanceTestFramework\XmlParser;

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
