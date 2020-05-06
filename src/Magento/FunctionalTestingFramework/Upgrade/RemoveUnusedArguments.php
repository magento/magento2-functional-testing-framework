<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\StaticCheck\ActionGroupArgumentsCheck;
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
        $actionGroupXmlFiles = $scriptUtil->getModuleXmlFilesByScope($testPaths,   DIRECTORY_SEPARATOR . 'ActionGroup' . DIRECTORY_SEPARATOR);
        $actionGroupsUpdated = 0;
        $fileSystem = new Filesystem();
        foreach ($actionGroupXmlFiles as $actionGroupXml) {
            $contents = $actionGroupXml->getContents();
            $domDocument = new \DOMDocument();
            $domDocument->load($actionGroupXml);
            $actionGroupArgumentsCheck = new ActionGroupArgumentsCheck();
            $unusedArgumentsFound = false;
            /** @var DOMElement $actionGroup */
            $actionGroup = $domDocument->getElementsByTagName('actionGroup')->item(0);
            $arguments = $actionGroupArgumentsCheck->extractActionGroupArguments($actionGroup);
            $unusedArguments = $actionGroupArgumentsCheck->findUnusedArguments($arguments, $contents);
            if (sizeof($unusedArguments) == 0) {
                continue;
            }
            foreach ($unusedArguments as $argument) {
                $unusedArgumentsFound = true;
                $contents = preg_replace("/\s*<argument.*".$argument.".*\/>/", "", $contents);
            }
            $newDomDocument = new \DOMDocument();
            $newDomDocument->loadXML($contents);
            if ($unusedArgumentsFound) {
                if ($newDomDocument->getElementsByTagName("argument")->length == 0) {
                    $contents = preg_replace("/\s*<arguments.*\/arguments>/s", "", $contents);
                }
                $fileSystem->dumpFile($actionGroupXml->getRealPath(), $contents);
                $actionGroupsUpdated++;

            }
        }
        return "Finished removing unused action group arguments from " . $actionGroupsUpdated . " files.";
    }
}