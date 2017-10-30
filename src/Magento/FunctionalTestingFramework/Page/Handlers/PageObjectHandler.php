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
    const URL = 'url';
    const MODULE = 'module';
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
        $parsedObjs = $parser->getData(self::TYPE);

        if (!$parsedObjs) {
            trigger_error("No " . self::TYPE . " objects defined", E_USER_NOTICE);
            return;
        }

        foreach ($parsedObjs as $pageName => $pageData) {
            $url = $pageData[self::URL];
            $module = $pageData[self::MODULE];
            $sections = array_keys($pageData[self::SUB_TYPE]);
            $parameterized = $pageData[self::PARAMETERIZED] ?? false;

            $this->pages[$pageName] = new PageObject($pageName, $url, $module, $sections, $parameterized);
        }
    }
}
