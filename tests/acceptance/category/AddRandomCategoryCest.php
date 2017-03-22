<?php

use Page\Acceptance\CategoryAdd as CategoryAdd;

/**
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class AddRandomCategoryCest
{
    public function _before(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
        $I->goToTheAdminProductsCategoriesPage();
        $I->waitForSpinnerToDisappear();
    }
    
    public function shouldBeAbleToAddARandomCategory(\Step\Acceptance\Admin $I, CategoryAdd $categoryAdd)
    {
        $categoryName = 'testCategory';
        
        $I->am('an Admin');
        $I->wantTo('verify that I can add a random Category');

        $categoryAdd->clickOnAddRootCategoryButton($I);
        $I->waitForSpinnerToDisappear();
        $categoryAdd->enterCategoryName($I, $categoryName);
        $categoryAdd->clickOnSaveButton($I);
        $I->waitForSpinnerToDisappear();
        $categoryAdd->verifyCategoryNameTitleIsCorrect($I, $categoryName);
    }
}
