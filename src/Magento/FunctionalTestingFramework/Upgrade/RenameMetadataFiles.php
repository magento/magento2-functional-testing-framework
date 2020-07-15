<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Class RenameMetadataFiles
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class RenameMetadataFiles implements UpgradeInterface
{
    /**
     * Upgrades all test xml files
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $scriptUtil = new ScriptUtil();
        $testPaths[] = $input->getArgument('path');
        if (empty($testPaths[0])) {
            $testPaths = $scriptUtil->getAllModulePaths();
        }

        foreach ($testPaths as $testsPath) {
            $finder = new Finder();
            $finder->files()->in($testsPath)->name("*-meta.xml");

            foreach ($finder->files() as $file) {
                $oldFileName = $file->getFileName();
                $newFileName = $this->convertFileName($oldFileName);
                $oldPath = $file->getPathname();
                $newPath = $file->getPath() . "/" . $newFileName;
                print("Renaming " . $oldPath . " => " . $newPath . "\n");
                rename($oldPath, $newPath);
            }
        }

        return "Finished renaming -meta.xml files.";
    }

    /**
     * Convert filenames like:
     *     user_role-meta.xml => UserRoleMeta.xml
     *     store-meta.xml => StoreMeta.xml
     *
     * @param string $oldFileName
     * @return string
     */
    private function convertFileName(string $oldFileName)
    {
        $stripEnding = preg_replace("/-meta.xml/", "", $oldFileName);
        $hyphenToUnderscore = str_replace("-", "_", $stripEnding);
        $parts = explode("_", $hyphenToUnderscore);
        $ucParts = [];
        foreach ($parts as $part) {
            $ucParts[] = ucfirst($part);
        }
        $recombine = join("", $ucParts);
        $addEnding = $recombine . "Meta.xml";
        return $addEnding;
    }
}
