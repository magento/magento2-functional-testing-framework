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
use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class SectionObjectHandler implements ObjectHandlerInterface
{
    const SECTION = 'section';
    const ELEMENT = 'element';
    const TYPE = 'type';
    const SELECTOR = 'selector';
    const LOCATOR_FUNCTION = 'locatorFunction';
    const TIMEOUT = 'timeout';
    const PARAMETERIZED = 'parameterized';
    const SECTION_NAME_ERROR_MSG = "Section names cannot contain non alphanumeric characters.\tSection='%s'";
    const ELEMENT_NAME_ERROR_MSG = "Element names cannot contain non alphanumeric characters.\tElement='%s'";

    /**
     * Singleton instance of this class
     *
     * @var SectionObjectHandler
     */
    private static $INSTANCE;

    /**
     * All section objects. Set during initialize().
     *
     * @var SectionObject[]
     */
    private $sectionObjects = [];

    /**
     * Constructor
     *
     * @constructor
     * @throws XmlException
     */
    private function __construct()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $parser = $objectManager->get(SectionParser::class);
        $parserOutput = $parser->getData(self::SECTION);

        if (!$parserOutput) {
            return;
        }

        foreach ($parserOutput as $sectionName => $sectionData) {
            $elements = [];

            if (preg_match('/[^a-zA-Z0-9_]/', $sectionName)) {
                throw new XmlException(sprintf(self::SECTION_NAME_ERROR_MSG, $sectionName));
            }

            try {
                foreach ($sectionData[SectionObjectHandler::ELEMENT] as $elementName => $elementData) {
                    if (preg_match('/[^a-zA-Z0-9_]/', $elementName)) {
                        throw new XmlException(sprintf(self::ELEMENT_NAME_ERROR_MSG, $elementName, $sectionName));
                    }
                    $elementType = $elementData[SectionObjectHandler::TYPE] ?? null;
                    $elementSelector = $elementData[SectionObjectHandler::SELECTOR] ?? null;
                    $elementLocatorFunc = $elementData[SectionObjectHandler::LOCATOR_FUNCTION] ?? null;
                    $elementTimeout = $elementData[SectionObjectHandler::TIMEOUT] ?? null;
                    $elementParameterized = $elementData[SectionObjectHandler::PARAMETERIZED] ?? false;

                    $elements[$elementName] = new ElementObject(
                        $elementName,
                        $elementType,
                        $elementSelector,
                        $elementLocatorFunc,
                        $elementTimeout,
                        $elementParameterized
                    );
                }
            } catch (XmlException $exception) {
                throw new XmlException($exception->getMessage() . " in Section '{$sectionName}'");
            }

            $this->sectionObjects[$sectionName] = new SectionObject($sectionName, $elements);
        }
    }

    /**
     * Initialize and/or return the singleton instance of this class
     *
     * @return SectionObjectHandler
     * @throws XmlException
     */
    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new SectionObjectHandler();
        }

        return self::$INSTANCE;
    }

    /**
     * Get a SectionObject by name
     *
     * @param string $name The section name.
     * @return SectionObject | null
     */
    public function getObject($name)
    {
        if (array_key_exists($name, $this->getAllObjects())) {
            return $this->getAllObjects()[$name];
        }

        return null;
    }

    /**
     * Get all SectionObjects
     *
     * @return SectionObject[]
     */
    public function getAllObjects()
    {
        return $this->sectionObjects;
    }
}
