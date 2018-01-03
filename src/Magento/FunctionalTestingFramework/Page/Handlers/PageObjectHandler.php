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

class PageObjectHandler implements ObjectHandlerInterface
{
    const PAGE = 'page';
    const SECTION = 'section';
    const URL = 'url';
    const MODULE = 'module';
    const PARAMETERIZED = 'parameterized';

    /**
     * The singleton instance of this class
     *
     * @var PageObjectHandler
     */
    private static $INSTANCE;

    /**
     * Array containing all page objects
     *
     * @var PageObject[]
     */
    private $pageObjects = [];

    /**
     * Private constructor
     */
    private function __construct()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $parser = $objectManager->get(PageParser::class);
        $parserOutput = $parser->getData(self::PAGE);

        if (!$parserOutput) {
            // No *Page.xml files found so give up
            return;
        }

        foreach ($parserOutput as $pageName => $pageData) {
            $url = $pageData[self::URL];
            $module = $pageData[self::MODULE];
            $sectionNames = array_keys($pageData[self::SECTION]);
            $parameterized = $pageData[self::PARAMETERIZED] ?? false;
            $this->pageObjects[$pageName] = new PageObject($pageName, $url, $module, $sectionNames, $parameterized);
        }
    }

    /**
     * Singleton method to return PageObjectHandler.
     *
     * @return PageObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new PageObjectHandler();
        }

        return self::$INSTANCE;
    }

    /**
     * Return a page object by name
     *
     * @param string $name
     * @return PageObject|null
     */
    public function getObject($name)
    {
        if (array_key_exists($name, $this->pageObjects)) {
            return $this->getAllObjects()[$name];
        }

        return null;
    }

    /**
     * Return all page objects
     *
     * @return PageObject[]
     */
    public function getAllObjects()
    {
        return $this->pageObjects;
    }
}
