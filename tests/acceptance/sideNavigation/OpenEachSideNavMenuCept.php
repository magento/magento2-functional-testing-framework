<?php
use Step\Acceptance\Admin as AdminTester;
use Page\Acceptance\SideNavigation as SideNav;

// @env firefox
// @env chrome
// @group slow
// @group skip
$I = new AdminTester($scenario);
$sideNavMenu = new SideNav($I);

$I->wantTo('see if I can open each of the Side Nav Menus');

$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();

$sideNavMenu->clickOnSalesInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheSalesNavMenu($I);

$sideNavMenu->clickOnProductsInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheProductNavMenu($I);

$sideNavMenu->clickOnCustomersInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheCustomersNavMenu($I);

$sideNavMenu->clickOnMarketingInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheMarketingNavMenu($I);

$sideNavMenu->clickOnContentInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheContentNavMenu($I);

$sideNavMenu->clickOnReportsInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheReportsNavMenu($I);

$sideNavMenu->clickOnStoresInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheStoresNavMenu($I);

$sideNavMenu->clickOnSystemInTheSideNavMenu($I);
$sideNavMenu->shouldSeeTheSystemNavMenu($I);