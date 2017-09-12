<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Handlers;

use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\XmlParser\PageParser;

/**
 * Class PageObjectHandler
 */
class PageObjectHandler implements ObjectHandlerInterface
{
    const TYPE = 'page';
    const SUB_TYPE = 'section';
    const URL_PATH_ATTR = 'urlPath';
    const MODULE_ATTR = 'module';
    const PARAMETERIZED = 'parameterized';

    /**
     * Singleton class variable instance
     *
     * @var PageObjectHandler
     */
    private static $PAGE_DATA_PROCESSOR;

    /**
     * Array containing all page objects
     *
     * @var array
     */
    private $pages = [];

    /**
     * Singleton method to return PageDataProcessor.
     *
     * @return PageObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$PAGE_DATA_PROCESSOR) {
            self::$PAGE_DATA_PROCESSOR = new PageObjectHandler();
            self::$PAGE_DATA_PROCESSOR->initPageObjects();
        }

        return self::$PAGE_DATA_PROCESSOR;
    }

    /**
     * PageObjectHandler constructor.
     */
    private function __construct()
    {
        //private constructor
    }

    /**
     * Takes a page name and returns an array parsed from xml.
     *
     * @param string $pageName
     * @return PageObject | null
     */
    public function getObject($pageName)
    {
        if (array_key_exists($pageName, $this->pages)) {
            return $this->getAllObjects()[$pageName];
        }

        return null;
    }

    /**
     * Return an array containing all pages parsed from xml.
     *
     * @return array
     */
    public function getAllObjects()
    {
        return $this->pages;
    }

    /**
     * Executes parser code to read in page xml data.
     *
     * @return void
     */
    private function initPageObjects()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        /** @var $parser \Magento\FunctionalTestingFramework\XmlParser\PageParser */
        $parser = $objectManager->get(PageParser::class);
        foreach ($parser->getData(self::TYPE) as $pageName => $pageData) {
            $urlPath = $pageData[PageObjectHandler::URL_PATH_ATTR];
            $module = $pageData[PageObjectHandler::MODULE_ATTR];
            $sections = array_keys($pageData[PageObjectHandler::SUB_TYPE]);
            $parameterized = $pageData[PageObjectHandler::PARAMETERIZED] ?? false;

            $this->pages[$pageName] = new PageObject($pageName, $urlPath, $module, $sections, $parameterized);
        }
    }
}
