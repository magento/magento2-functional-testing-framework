<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class GenerateDevUrnCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:urn-catalog')
            ->setDescription('This command generates an URN catalog to enable PHPStorm to recognize and highlight URNs.')
            ->addArgument('path', InputArgument::REQUIRED, 'path to PHPStorm misc.xml file (typically located in [ProjectRoot]/.idea/misc.xml)')
            ->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'forces creation of misc.xml file if not found in the path given.'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $miscXmlFilePath = $input->getArgument('path') . DIRECTORY_SEPARATOR . "misc.xml";
        $miscXmlFile = realpath($miscXmlFilePath);
        $force = $input->getOption('force');

        if ($miscXmlFile === false) {
            if ($force == true) {
                // create file and refresh realpath
                $xml = "<project version=\"4\"/>";
                file_put_contents($miscXmlFilePath, $xml);
                $miscXmlFile = realpath($miscXmlFilePath);
            } else {
                $exceptionMessage = "misc.xml not found in given path '{$miscXmlFilePath}'";
                LoggingUtil::getInstance()->getLogger(GenerateDevUrnCommand::class)
                    ->error($exceptionMessage);
                throw new TestFrameworkException($exceptionMessage);
            }
        }
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML(file_get_contents($miscXmlFile));

        //Locate ProjectResources node, create one if none are found.
        $nodeForWork = null;
        foreach($dom->getElementsByTagName('component') as $child) {
            if ($child->getAttribute('name') === 'ProjectResources') {
                $nodeForWork = $child;
            }
        }
        if ($nodeForWork === null) {
            $project = $dom->getElementsByTagName('project')->item(0);
            $nodeForWork = $dom->createElement('component');
            $nodeForWork->setAttribute('name', 'ProjectResources');
            $project->appendChild($nodeForWork);
        }

        //Extract url=>location mappings that already exist, add MFTF URNs and reappend
        $resources = [];
        $resourceNodes = $nodeForWork->getElementsByTagName('resource');
        $resourceCount = $resourceNodes->length;
        for ($i = 0; $i < $resourceCount; $i++) {
            $child = $resourceNodes[0];
            $resources[$child->getAttribute('url')] = $child->getAttribute('location');
            $child->parentNode->removeChild($child);
        }

        $resources = array_merge($resources, $this->generateResourcesArray());

        foreach ($resources as $url => $location) {
            $resourceNode = $dom->createElement('resource');
            $resourceNode->setAttribute('url', $url);
            $resourceNode->setAttribute('location', $location);
            $nodeForWork->appendChild($resourceNode);
        }

        //Save output
        $dom->save($miscXmlFile);
        $output->writeln("MFTF URN mapping successfully added to {$miscXmlFile}.");
    }

    /**
     * Generates urn => location array for all MFTF schema.
     * @return array
     */
    private function generateResourcesArray()
    {
        $resourcesArray = [
            'urn:magento:mftf:DataGenerator/etc/dataOperation.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataOperation.xsd'),
            'urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/DataGenerator/etc/dataProfileSchema.xsd'),
            'urn:magento:mftf:Page/etc/PageObject.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/Page/etc/PageObject.xsd'),
            'urn:magento:mftf:Page/etc/SectionObject.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/Page/etc/SectionObject.xsd'),
            'urn:magento:mftf:Test/etc/actionGroupSchema.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/Test/etc/actionGroupSchema.xsd'),
            'urn:magento:mftf:Test/etc/testSchema.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/Test/etc/testSchema.xsd'),
            'urn:magento:mftf:Suite/etc/suiteSchema.xsd' =>
                realpath(FW_BP . '/src/Magento/FunctionalTestingFramework/Suite/etc/suiteSchema.xsd')
        ];
        return $resourcesArray;
    }

}
