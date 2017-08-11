<?php
namespace Magento\AcceptanceTestFramework\PageObject\Page;

use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\XmlParser\PageParser;

class Page implements PageInterface
{
    const SUB_TYPE = 'section';
    const URL_PATH_ATTR = 'urlPath';
    const URL_PATH_VARS_ATTR = 'urlPathVariables';
    const MODULE_ATTR = 'module';

    /**
     * @var array
     */
    private static $pageObjects = [];

    /**
     * Get page object data. All pages data is returned if $name is not specified.
     *
     * @param string $pageName [optional]
     * @return mixed
     */
    public static function getPage($pageName = null)
    {
        self::initPageObjects();
        if (!$pageName) {
            return self::$pageObjects;
        }

        if (array_key_exists($pageName, self::$pageObjects)) {
            return self::$pageObjects[$pageName];
        }

        return null;
    }

    /**
     * Get relative url (excluding base url) of a page.
     *
     * @param string $pageName
     * @return string
     */
    public static function getPageUrl($pageName)
    {
        $page = self::getPage($pageName);
        $urlPath = $page[self::URL_PATH_ATTR];
        if (isset($page[self::URL_PATH_VARS_ATTR])) {
            $urlPathVarsStr = $page[self::URL_PATH_VARS_ATTR];
            $urlPathVarsArray = explode(',', $urlPathVarsStr);
            $newUrlPathVarsArray = [];
            foreach ($urlPathVarsArray as $variable) {
                $variable = trim($variable);
                if (substr($variable, 0, 1) !== '$') {
                    $variable = '$' . $variable;
                    $newUrlPathVarsArray[] = $variable;
                }
            }
            $urlPathVarsStr = implode(',', $newUrlPathVarsArray);
            return 'sprintf("' . $urlPath . '", ' . $urlPathVarsStr . ')';
        } else {
            return $urlPath;
        }
    }

    /**
     * Get section names of a page.
     *
     * @param string $pageName
     * @return array|null
     */
    public static function getSectionNamesInPage($pageName)
    {
        return array_keys(self::getPage($pageName)[self::SUB_TYPE]);
    }

    /**
     * Get magento module name of a page.
     *
     * @param string $pageName
     * @return string
     */
    public static function getPageMagentoModuleName($pageName)
    {
        return self::getPage($pageName)[self::MODULE_ATTR];
    }

    /**
     * Parse page objects if it's not previously done.
     *
     * @return void
     */
    private static function initPageObjects()
    {
        if (empty(self::$pageObjects)) {
            $objectManager = ObjectManagerFactory::getObjectManager();
            /** @var $parser \Magento\AcceptanceTestFramework\XmlParser\PageParser */
            $parser = $objectManager->get(PageParser::class);
            self::$pageObjects = $parser->getData(self::TYPE);
        }
    }
}
