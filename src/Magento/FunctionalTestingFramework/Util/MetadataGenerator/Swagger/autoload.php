<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require '../../../../../../vendor/autoload.php';

AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    "../../../../../vendor/jms/serializer/src"
);

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
