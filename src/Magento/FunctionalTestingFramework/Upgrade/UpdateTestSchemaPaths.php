<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateTestSchemaPaths
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class UpdateTestSchemaPaths implements UpgradeInterface
{
    /**
     * OutputInterface
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Total test updated
     *
     * @var integer
     */
    private $testsUpdated = 0;

    /**
     * Entity type to urn map
     *
     * @var array
     */
    private $typeToUrns = [
        'ActionGroup' => 'urn:magento:mftf:Test/etc/actionGroupSchema.xsd',
        'Data' => 'urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd',
        'Metadata' => 'urn:magento:mftf:DataGenerator/etc/dataOperation.xsd',
        'Page' => 'urn:magento:mftf:Page/etc/PageObject.xsd',
        'Section' => 'urn:magento:mftf:Page/etc/SectionObject.xsd',
        'Suite' => 'urn:magento:mftf:Suite/etc/suiteSchema.xsd',
        'Test' => 'urn:magento:mftf:Test/etc/testSchema.xsd',
    ];

    /**
     * Upgrades all test xml files, replacing relative schema paths to URN.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string
     * @throws TestFrameworkException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $scriptUtil = new ScriptUtil();
        $this->output = $output;
        $this->testsUpdated = 0;
        $testPaths[] = $input->getArgument('path');
        if (empty($testPaths[0])) {
            $testPaths = $scriptUtil->getAllModulePaths();
        }

        // Process module xml files
        foreach ($this->typeToUrns as $type => $urn) {
            $xmlFiles = $scriptUtil->getModuleXmlFilesByScope($testPaths, $type);
            $this->processXmlFiles($xmlFiles, $urn);
        }

        return ("Schema Path updated to use MFTF URNs in {$this->testsUpdated} file(s).");
    }

    /**
     * Convert xml schema location from non urn based to urn based
     *
     * @param Finder $xmlFiles
     * @param string $urn
     * @return void
     */
    private function processXmlFiles($xmlFiles, $urn)
    {
        $pattern = '/xsi:noNamespaceSchemaLocation[\s]*=[\s]*"(?<urn>[^\<\>"\']*)"/';
        foreach ($xmlFiles as $file) {
            $filePath = $file->getRealPath();
            $contents = $file->getContents();
            preg_match($pattern, $contents, $matches);
            if (isset($matches['urn'])) {
                if (trim($matches['urn']) !== $urn) {
                    file_put_contents($filePath, str_replace($matches['urn'], $urn, $contents));
                    $this->testsUpdated++;
                }
            }
        }
    }
}
