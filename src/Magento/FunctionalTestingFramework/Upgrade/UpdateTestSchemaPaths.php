<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class UpdateTestSchemaPaths
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class UpdateTestSchemaPaths implements UpgradeInterface
{
    /**
     * Upgrades all test xml files, replacing relative schema paths to URN.
     *
     * @param InputInterface $input
     * @return string
     */
    public function execute(InputInterface $input)
    {
        // @codingStandardsIgnoreStart
        $relativeToUrn = [
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataOperation.xsd"
                => "urn:magento:mftf:DataGenerator/etc/dataOperation.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataProfileSchema.xsd"
                => "urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/Page/etc/PageObject.xsd"
                => "urn:magento:mftf:Page/etc/PageObject.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/Page/etc/SectionObject.xsd"
                => "urn:magento:mftf:Page/etc/SectionObject.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/Test/etc/actionGroupSchema.xsd"
                => "urn:magento:mftf:Test/etc/actionGroupSchema.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/Test/etc/testSchema.xsd"
                => "urn:magento:mftf:Test/etc/testSchema.xsd",
            "dev/tests/acceptance/vendor/magento/magento2-functional-testing-framework/src/Magento/FunctionalTestingFramework/Suite/etc/suiteSchema.xsd"
                => "urn:magento:mftf:Suite/etc/suiteSchema.xsd"
        ];
        // @codingStandardsIgnoreEnd

        $relativePatterns = [];
        $urns = [];
        // Prepare array of patterns to URNs for preg_replace (replace / to escapes
        foreach ($relativeToUrn as $relative => $urn) {
            $relativeReplaced = str_replace('/', '\/', $relative);
            $relativePatterns[] = '/[.\/]+' . $relativeReplaced  . '/';
            $urns[] = $urn;
        }

        $testsPath = $input->getArgument('path');
        $finder = new Finder();
        $finder->files()->in($testsPath)->name("*.xml");

        $fileSystem = new Filesystem();
        $testsUpdated = 0;
        foreach ($finder->files() as $file) {
            $count = 0;
            $contents = $file->getContents();
            $contents = preg_replace($relativePatterns, $urns, $contents, -1, $count);
            $fileSystem->dumpFile($file->getRealPath(), $contents);
            if ($count > 0) {
                $testsUpdated++;
            }
        }

        return ("Schema Path updated to use MFTF URNs in {$testsUpdated} file(s).");
    }
}
