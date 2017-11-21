<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
require_once 'autoload.php';

// create a MetadataGenerator Object
$generator = new Magento\FunctionalTestingFramework\Util\MetadataGenerator\Swagger\MetadataGenerator();
$generator->generateMetadataFromSwagger();
