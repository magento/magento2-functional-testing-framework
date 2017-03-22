<?php
use Step\Acceptance\Admin;

// @group skip
$I = new Admin($scenario);
$I->wantTo('demo the usage of StepObject in Cept');
$I->goToTheAdminLoginPage();
$I->loginAsAnExistingAdmin();