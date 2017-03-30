<?php

// @group skip
$I = new \Magento\Xxyyzz\Step\Backend\Admin($scenario);
$I->wantTo('demo the usage of StepObject in Cept');
$I->goToTheAdminLoginPage();
$I->loginAsAdmin();