<?php
// @env firefox
// @env chrome
// @env phantomjs
// @group smoke
use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->wantTo('make sure you cannot access Admin pages after logging out');

$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();
$I->goToTheAdminLogoutPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();