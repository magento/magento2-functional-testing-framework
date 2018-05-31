<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('path', InputArgument::REQUIRED, 'path to PHPStorm misc.xml file (typically located in [ProjectRoot]/.idea/misc.xml)');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $miscXmlFilePath = $input->getArgument('path') . DIRECTORY_SEPARATOR . "misc.xml";
        $miscXmlFile = realpath($miscXmlFilePath);

        if ($miscXmlFile === false) {
            throw new TestFrameworkException("misc.xml not found in given path '{$miscXmlFilePath}'");
        }

        $xml = simplexml_load_file($miscXmlFile);

        //Locate ProjectResources node, create one if none are found.
        $nodeForWork = null;
        foreach($xml->component as $child) {
            if ((string)$child->attributes()->name === 'ProjectResources') {
                $nodeForWork = $child;
            }
        }
        if ($nodeForWork === null) {
            $nodeForWork = new \SimpleXMLElement('<component name="ProjectResources"/>');
        }

        //Extract url=>location mappings that already exist, add MFTF URNs and reappend
        $resources = [];
        foreach($nodeForWork->xpath("//resource") as $child) {
            $resources[(string)$child->attributes()->url] = (string)$child->attributes()->location;
            $this->removeFromXml($child);
        }

        $resources = array_merge($resources, $this->generateResourcesArray());

        foreach ($resources as $url => $location) {
            $resourceNode = new \SimpleXMLElement('<resource/>');
            $resourceNode->addAttribute('url', $url);
            $resourceNode->addAttribute('location', $location);
            $this->appendToXml($nodeForWork, $resourceNode);
        }

        //Remove old node and reappend
        $this->removeFromXml($nodeForWork);
        $this->appendToXml($xml, $nodeForWork);

        //Format and save output
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($miscXmlFile);
        $output->writeln("MFTF URN mapping successfully added to {$miscXmlFile}.");
    }

    /**
     * Appends SimpleXmlElement to the given SimpleXmlElement parent.
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     * @return void
     */
    private function appendToXml(\SimpleXMLElement $to, \SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * Remove given SimleXmlElement child from child's parent node.
     * @param \SimpleXMLElement $child
     * @return void
     */
    private function removeFromXml(\SimpleXMLElement $child) {
        $dom = dom_import_simplexml($child);
        $dom->parentNode->removeChild($dom);
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
