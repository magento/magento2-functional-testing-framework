<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\PageObject\Page;

/**
 * Interface for Page.
 */
interface PageInterface
{
    const TYPE = 'page';

    /**
     * Get page object data. All pages data is returned if $name is not specified.
     *
     * @param string $pageName [optional]
     * @return array|null
     */
    public static function getPage($pageName = null);

    /**
     * Get relative url (excluding base url) of a page.
     *
     * @param string $pageName
     * @return string
     */
    public static function getPageUrl($pageName);

    /**
     * Get magento module name of a page.
     *
     * @param string $pageName
     * @return string
     */
    public static function getPageMagentoModuleName($pageName);
    
    /**
     * Get section names of a page.
     *
     * @param string $pageName
     * @return array|null
     */
    public static function getSectionNamesInPage($pageName);
}
