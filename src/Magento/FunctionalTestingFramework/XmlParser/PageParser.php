<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\XmlParser;

use Magento\FunctionalTestingFramework\Config\DataInterface;
use Magento\FunctionalTestingFramework\ObjectManagerInterface;

/**
 * Generic Xml Parser.
 */
class PageParser implements ParserInterface
{
    /**
     * Object manager.
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * Configuration data.
     *
     * @var DataInterface
     */
    protected $configData;

    /**
     * PageParser Constructor
     * @param ObjectManagerInterface $objectManager
     * @param DataInterface          $configData
     */
    public function __construct(ObjectManagerInterface $objectManager, DataInterface $configData)
    {
        $this->objectManager = $objectManager;
        $this->configData = $configData;
    }

    /**
     * Get parsed xml data.
     * @param string $type
     * @return array
     */
    public function getData($type)
    {
        return $this->configData->get($type);
    }
}
