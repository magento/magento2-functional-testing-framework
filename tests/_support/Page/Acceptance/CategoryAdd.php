<?php
namespace Page\Acceptance;

class CategoryAdd
{
    // include url of current page
    public static $URL = '';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */
    public static $addRootCategoryButton = '#add_root_category_button';
    public static $categoryNameField = 'input[name="name"]';
    public static $categoryNameTitle = '.page-title';

    public static $saveButton = '#save';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @var \AcceptanceTester;
     */
    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    public function clickOnAddRootCategoryButton(\AcceptanceTester $I)
    {
        $I->click(self::$addRootCategoryButton);
    }

    public function enterCategoryName(\AcceptanceTester $I, $categoryName)
    {
        $I->fillField(self::$categoryNameField, $categoryName);
    }

    public function clickOnSaveButton(\AcceptanceTester $I)
    {
        $I->click(self::$saveButton);
    }

    public function verifyCategoryNameTitleIsCorrect(\AcceptanceTester $I, $categoryName)
    {
        $I->see($categoryName, self::$categoryNameTitle);
    }

}
