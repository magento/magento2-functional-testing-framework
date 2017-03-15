<?php
use Step\Acceptance\Admin as AdminTester;
use Page\Acceptance\SideNavigation as SideNav;

// @env firefox
// @env chrome
// @group slow
$I = new AdminTester($scenario);
$sideNavMenu = new SideNav($I);

$I->wantTo('see if I can open each of the Side Nav Menus');

$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();

$sideNavMenu->iClickOnSalesInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheSalesNavMenu($I);

$sideNavMenu->iClickOnProductsInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheProductNavMenu($I);

$sideNavMenu->iClickOnCustomersInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheCustomersNavMenu($I);

$sideNavMenu->iClickOnMarketingInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheMarketingNavMenu($I);

$sideNavMenu->iClickOnContentInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheContentNavMenu($I);

$sideNavMenu->iClickOnReportsInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheReportsNavMenu($I);

$sideNavMenu->iClickOnStoresInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheStoresNavMenu($I);

$sideNavMenu->iClickOnSystemInTheSideNavMenu($I);
$sideNavMenu->iShouldSeeTheSystemNavMenu($I);