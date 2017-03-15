<?php
// @env firefox
// @env chrome
// @env phantomjs
use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('logout of the Admin area and land on the Login page');

$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();
$I->goToTheAdminLogoutPage();
$I->shouldBeOnTheAdminLoginPage();