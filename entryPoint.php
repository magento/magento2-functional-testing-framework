<?php

require_once 'bootstrap.php';

/** @var Magento\AcceptanceTestFramework\Dummy $dummy */
$dummy = $objectManager->create(\Magento\AcceptanceTestFramework\Dummy::class);
$dummy->readPageObjects();
