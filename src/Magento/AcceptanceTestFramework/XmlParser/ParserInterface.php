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
     * @return mixed
     */
    public function getData($type);
}
