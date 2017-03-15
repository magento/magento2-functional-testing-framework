<?php
// @env firefox
// @env chrome
// @env phantomjs
use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);

$I->wantTo('make sure you cannot access Admin pages when NOT logged in');
$I->goToTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();

$I->goToRandomAdminPage();
$I->shouldBeOnTheAdminLoginPage();