<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Handlers;

use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\XmlParser\PageParser;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class PageObjectHandler implements ObjectHandlerInterface
{
    const PAGE = 'page';
    const SECTION = 'section';
    const URL = 'url';
    const MODULE = 'module';
    const PARAMETERIZED = 'parameterized';
    const AREA = 'area';
    const FILENAME = 'filename';
    const NAME_BLACKLIST_ERROR_MSG = "Page names cannot contain non alphanumeric characters.\tPage='%s'";

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
     *
     * @throws XmlException
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
            if (preg_match('/[^a-zA-Z0-9_]/', $pageName)) {
                throw new XmlException(sprintf(self::NAME_BLACKLIST_ERROR_MSG, $pageName));
            }
            $area = $pageData[self::AREA] ?? null;
            $url = $pageData[self::URL] ?? null;

            if ($area == 'admin') {
                $url = ltrim($url, "/");
            }

            $module = $pageData[self::MODULE] ?? null;
            $sectionNames = array_keys($pageData[self::SECTION] ?? []);
            $parameterized = $pageData[self::PARAMETERIZED] ?? false;
            $filename = $pageData[self::FILENAME] ?? null;
            $deprecated = $pageData[self::OBJ_DEPRECATED] ?? null;

            if ($deprecated !== null) {
                LoggingUtil::getInstance()->getLogger(self::class)->deprecation(
                    $deprecated,
                    ["pageName" => $filename, "deprecatedPage" => $deprecated]
                );
            }

            $this->pageObjects[$pageName] =
                new PageObject($pageName, $url, $module, $sectionNames, $parameterized, $area, $filename, $deprecated);
        }
    }

    /**
     * Singleton method to return PageObjectHandler.
     *
     * @return PageObjectHandler
     * @throws XmlException
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
