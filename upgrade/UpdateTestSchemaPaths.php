<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Symfony\Component\Finder\Finder;

$relativeToUrn = [
    "/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataOperation.xsd"
        => "urn:magento:mftf:DataGenerator/etc/dataOperation.xsd",
    "/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataProfileSchema.xsd"
        => "urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd",
    "/src/Magento/FunctionalTestingFramework/Page/etc/PageObject.xsd"
        => "urn:magento:mftf:Page/etc/PageObject.xsd",
    "/src/Magento/FunctionalTestingFramework/Page/etc/SectionObject.xsd"
        => "urn:magento:mftf:Page/etc/SectionObject.xsd",
    "/src/Magento/FunctionalTestingFramework/Test/etc/actionGroupSchema.xsd"
        => "urn:magento:mftf:Test/etc/actionGroupSchema.xsd",
    "/src/Magento/FunctionalTestingFramework/Test/etc/testSchema.xsd"
        => "urn:magento:mftf:Test/etc/testSchema.xsd",
    "/src/Magento/FunctionalTestingFramework/Suite/etc/suiteSchema.xsd"
        => "urn:magento:mftf:Suite/etc/suiteSchema.xsd"
];

$relativePatterns = [];
$urns = [];
// Prepare array of patterns to URNs for preg_replace
foreach ($relativeToUrn as $relative => $urn) {
    $relativeReplaced = str_replace('/', '\/', $relative);
    $relativePatterns[] = '/[.\/]+' . $relativeReplaced  . '/';
    $urns[] = $urn;
}

$testsPath = $input->getArgument('path');
$testsUpdated = 0;

$testMaterials = new Finder();
$testMaterials->files()->in($testsPath)->name("*.xml");

foreach($testMaterials->files() as $file) {
    $count = 0;
    $contents = $file->getContents();
    $contents = preg_replace($relativePatterns, $urns, $contents, -1, $count);
    $fileSystem->dumpFile($file->getRealPath(), $contents);
    if ($count > 0) {
        $testsUpdated++;
    }
}

return ("Schema Path updated to use MFTF URNs in {$testsUpdated} file(s).");