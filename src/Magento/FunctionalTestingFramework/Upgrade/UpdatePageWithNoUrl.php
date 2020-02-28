<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class UpgradePageWithNoUrl
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class UpdatePageWithNoUrl implements UpgradeInterface
{
    /**
     * Upgrades all test xml files, replacing {{page}} with {{page.url}}
     *
     * @param InputInterface $input
     * @return string
     */
    public function execute(InputInterface $input)
    {
        $testsPath = $input->getArgument('path');
        $finder = new Finder();
        $finder->files()->in($testsPath)->name("*.xml");

        // Safely ensure we can get test materials to check for existing pages later
        try {
            MftfApplicationConfig::create();
            ModuleResolver::getInstance()->getModulesPath();
        } catch (\Exception $e) {
            //retry with --force
            MftfApplicationConfig::create(true);
            ModuleResolver::getInstance()->getModulesPath();
        }

        $fileSystem = new Filesystem();
        $testsUpdated = 0;
        foreach ($finder->files() as $file) {
            $count = 0;
            $contents = $file->getContents();
            // Find {{page}} but not {{page.url}}
            preg_match_all("/{{([^\.}]*)}}/", $contents, $pageReferences);
            if (empty($pageReferences[1])) {
                continue;
            }
            foreach ($pageReferences[1] as $index => $potentialReplace) {
                $isPage = PageObjectHandler::getInstance()->getObject($potentialReplace);
                if ($isPage === null) {
                    continue;
                }
                $contents = str_replace($pageReferences[0][$index], "{{" . $potentialReplace . ".url}}", $contents);
                $count++;
            }
            $fileSystem->dumpFile($file->getRealPath(), $contents);
            if ($count > 0) {
                $testsUpdated++;
            }
        }
        return ("{{Page}} without specified \".url\" updated in {$testsUpdated} file(s).");
    }
}
