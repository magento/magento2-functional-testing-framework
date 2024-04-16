<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\StaticCheck\ActionGroupStandardsCheck;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use DOMElement;

/**
 * Class RenameMetadataFiles
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class RemoveUnusedArguments implements UpgradeInterface
{
    const ARGUMENTS_BLOCK_REGEX_PATTERN = "/\s*<arguments.*\/arguments>/s";

    /**
     * Updates all actionGroup xml files
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
        $xmlFiles = $scriptUtil->getModuleXmlFilesByScope($testPaths, 'ActionGroup');
        $actionGroupsUpdated = 0;
        $fileSystem = new Filesystem();
        foreach ($xmlFiles as $file) {
            $contents = $file->getContents();
            $argumentsCheck = new ActionGroupStandardsCheck();
            /** @var DOMElement $actionGroup */
            $actionGroup = $argumentsCheck->getActionGroupDomElement($contents);
            $allArguments = $argumentsCheck->extractActionGroupArguments($actionGroup);
            $unusedArguments = $argumentsCheck->findUnusedArguments($allArguments, $contents);
            if (empty($unusedArguments)) {
                continue;
            }
            //Remove <arguments> block if all arguments are unused
            if (empty(array_diff($allArguments, $unusedArguments))) {
                $contents = preg_replace(self::ARGUMENTS_BLOCK_REGEX_PATTERN, '', $contents);
            } else {
                foreach ($unusedArguments as $argument) {
                    $argumentRegexPattern = "/\s*<argument.*name\s*=\s*\"".$argument."\".*\/>/";
                    $contents = preg_replace($argumentRegexPattern, '', $contents);
                }
            }
            $fileSystem->dumpFile($file->getRealPath(), $contents);
            $actionGroupsUpdated++;
        }
        return "Removed unused action group arguments from {$actionGroupsUpdated} file(s).";
    }
}
