<?php
namespace Page\Acceptance;

class AdminCategoryPage
{
    // include url of current page
    public static $URL = '/admin/catalog/category/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $pageTitle                = '.page-title';
    public static $scheduleNewUpdateButton  = '#staging_update_new';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function amOnAdminCategoryPageById(\AcceptanceTester $I, $id) {
        $I->amOnPage(self::$URL . 'edit/id/' . $id);
    }

    public function seeCategoryNameInPageTitle(\AcceptanceTester $I, $name) {
        $I->see($name, self::$pageTitle);
    }
}
