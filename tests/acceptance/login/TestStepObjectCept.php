<?php
// @env firefox
// @env chrome
// @env phantomjs
use Step\Acceptance\Admin;

$I = new Admin($scenario);
$I->wantTo('demo the usage of StepObject in Cept');
$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();