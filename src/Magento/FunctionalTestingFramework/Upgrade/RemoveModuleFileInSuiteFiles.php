<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

/**
 * Class RemoveModuleFileInSuiteFiles
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class RemoveModuleFileInSuiteFiles implements UpgradeInterface
{
    /**
     * OutputInterface
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Console output style
     *
     * @var SymfonyStyle
     */
    private $ioStyle = null;

    /**
     * Indicate if notice is print
     *
     * @var boolean
     */
    private $printNotice = false;

    /**
     * Number of test being updated
     *
     * @var integer
     */
    private $testsUpdated = 0;

    /**
     * Indicate if a match and replace has happened
     *
     * @var boolean
     */
    private $replaced = false;

    /**
     * Scan all suite xml files, remove <module file="".../> node, and print update message
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string
     * @throws TestFrameworkException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $scriptUtil = new ScriptUtil();
        $this->setOutputStyle($input, $output);
        $this->output = $output;
        $testPaths[] = $input->getArgument('path');
        if (empty($testPaths[0])) {
            $testPaths = $scriptUtil->getAllModulePaths();
        }

        // Get module suite xml files
        $xmlFiles = $scriptUtil->getModuleXmlFilesByScope($testPaths, 'Suite');
        $this->processXmlFiles($xmlFiles);

        return ("Removed module file reference in {$this->testsUpdated} suite file(s).");
    }

    /**
     * Process on list of xml files
     *
     * @param Finder $xmlFiles
     * @return void
     */
    private function processXmlFiles($xmlFiles)
    {
        foreach ($xmlFiles as $file) {
            $contents = $file->getContents();
            $filePath = $file->getRealPath();
            $this->replaced = false;
            $contents = $this->removeModuleFileAttributeInSuite($contents, $filePath);
            if ($this->replaced) {
                file_put_contents($filePath, $contents);
                $this->testsUpdated++;
            }
        }
    }

    /**
     * Remove module file attribute in Suite xml file
     *
     * @param string $contents
     * @param string $file
     * @return string|string[]|null
     */
    private function removeModuleFileAttributeInSuite($contents, $file)
    {
        $pattern = '/<module[^\<\>]+file[\s]*=[\s]*"(?<file>[^"\<\>]*)"[^\>\<]*>/';
        $contents = preg_replace_callback(
            $pattern,
            function ($matches) use ($file) {
                if (!$this->printNotice) {
                    $this->ioStyle->note(
                        '`file` is not a valid attribute for <module> in Suite XML schema.' . PHP_EOL
                        . 'The `file`references in the following xml files are commented out. '
                        . 'Consider using <test> instead.'
                    );
                    $this->printNotice = true;
                }
                $this->output->writeln(
                    PHP_EOL
                    . '"' . trim($matches[0]) . '"' . PHP_EOL
                    . 'is commented out from file: ' . $file . PHP_EOL
                );
                $result = str_replace('<module', '<!--module', $matches[0]);
                $result = str_replace('>', '--> <!-- Please replace with <test name="" -->', $result);
                $this->replaced = true;
                return $result;
            },
            $contents
        );
        return $contents;
    }

    /**
     * Set Symfony Style for output
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    private function setOutputStyle(InputInterface $input, OutputInterface $output)
    {
        // For output style
        if (null === $this->ioStyle) {
            $this->ioStyle = new SymfonyStyle($input, $output);
        }
    }
}
