<?php
// @env firefox
// @env chrome
// @env phantomjs
// @group smoke
use Step\Acceptance\Admin as AdminTester;

$I = new AdminTester($scenario);
$I->am('an Admin');
$I->wantTo('verify that I can access the Admin Login page');

$I->goToTheAdminLoginPage();
$I->shouldBeOnTheAdminLoginPage();