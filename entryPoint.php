<?php

require_once 'bootstrap.php';

/** @var Magento\Xxyyzz\Dummy $dummy */
$dummy = $objectManager->create(\Magento\Xxyyzz\Dummy::class);
$dummy->readPageObjects();
