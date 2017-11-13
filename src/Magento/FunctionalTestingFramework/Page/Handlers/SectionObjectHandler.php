<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Handlers;

use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\XmlParser\SectionParser;

/**
 * Class SectionObjectHandler
 */
class SectionObjectHandler implements ObjectHandlerInterface
{
    const TYPE = 'section';
    const SUB_TYPE = 'element';
    const ELEMENT_TYPE_ATTR = 'type';
    const ELEMENT_SELECTOR_ATTR = 'selector';
    const ELEMENT_LOCATOR_FUNC_ATTR = 'locatorFunction';
    const ELEMENT_TIMEOUT_ATTR = 'timeout';
    const ELEMENT_PARAMETERIZED = 'parameterized';

    /**
     * Singleton variable instance of class
     *
     * @var SectionObjectHandler
     */
    private static $SECTION_DATA_PROCESSOR;

    /**
     * Array containing all Section Objects
     *
     * @var array
     */
    private $sectionData = [];

    /**
     * Singleton method to return SectionArrayProcesor.
     *
     * @return SectionObjectHandler
     */
    public static function getInstance()
    {
        if (! self::$SECTION_DATA_PROCESSOR) {
            self::$SECTION_DATA_PROCESSOR = new SectionObjectHandler();
            self::$SECTION_DATA_PROCESSOR->initSectionObjects();
        }

        return self::$SECTION_DATA_PROCESSOR;
    }

    /**
     * SectionObjectHandler constructor.
     * @constructor
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Returns the corresponding section array parsed from xml.
     *
     * @param string $sectionName
     * @return SectionObject | null
     */
    public function getObject($sectionName)
    {
        if (array_key_exists($sectionName, $this->getAllObjects())) {
            return $this->getAllObjects()[$sectionName];
        }

        return null;
    }

    /**
     * Returns all section arrays parsed from section xml.
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->sectionData;
    }

    /**
     * Parse section objects if it's not previously done.
     *
     * @return void
     */
    private function initSectionObjects()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        /** @var $parser \Magento\FunctionalTestingFramework\XmlParser\SectionParser */
        $parser = $objectManager->get(SectionParser::class);
        $parsedObjs = $parser->getData(self::TYPE);

        if (!$parsedObjs) {
            trigger_error("No " . self::TYPE . " objects defined", E_USER_NOTICE);
            return;
        }

        foreach ($parsedObjs as $sectionName => $sectionData) {
            // create elements
            $elements = [];
            foreach ($sectionData[SectionObjectHandler::SUB_TYPE] as $elementName => $elementData) {
                $elementType = $elementData[SectionObjectHandler::ELEMENT_TYPE_ATTR];
                $elementSelector = $elementData[SectionObjectHandler::ELEMENT_SELECTOR_ATTR] ?? null;
                $elementLocatorFunc = $elementData[SectionObjectHandler::ELEMENT_LOCATOR_FUNC_ATTR] ?? null;
                $elementTimeout = $elementData[SectionObjectHandler::ELEMENT_TIMEOUT_ATTR] ?? null;
                $elementParameterized = $elementData[SectionObjectHandler::ELEMENT_PARAMETERIZED] ?? false;

                $elements[$elementName] = new ElementObject(
                    $elementName,
                    $elementType,
                    $elementSelector,
                    $elementLocatorFunc,
                    $elementTimeout,
                    $elementParameterized
                );
            }

            $this->sectionData[$sectionName] = new SectionObject($sectionName, $elements);
        }
    }
}
