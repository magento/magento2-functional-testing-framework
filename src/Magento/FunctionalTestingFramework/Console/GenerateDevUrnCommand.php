<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

class GenerateDevUrnCommand extends Command
{
    private const SUCCESS_EXIT_CODE = 0;
    /**
     * Argument for the path to IDE config file
     */
    public const IDE_FILE_PATH_ARGUMENT = 'path';

    public const PROJECT_PATH_IDENTIFIER = '$PROJECT_DIR$';
    public const MFTF_SRC_PATH = 'src/Magento/FunctionalTestingFramework/';

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:urn-catalog')
            ->setDescription('Generates the catalog of URNs to *.xsd mappings for the IDE to highlight xml.')
            ->addArgument(
                self::IDE_FILE_PATH_ARGUMENT,
                InputArgument::REQUIRED,
                'Path to file to output the catalog. For PhpStorm use .idea/misc.xml'
            )
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
     * @return int
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $miscXmlFilePath = $input->getArgument(self::IDE_FILE_PATH_ARGUMENT);
        $miscXmlFile = realpath($miscXmlFilePath);
        $force = (bool) $input->getOption('force');

        if ($miscXmlFile === false) {
            if ($force === true) {
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
        foreach ($dom->getElementsByTagName('component') as $child) {
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

        return self::SUCCESS_EXIT_CODE;
    }

    /**
     * Generates urn => location array for all MFTF schema.
     *
     * @return array
     */
    private function generateResourcesArray()
    {
        $resourcesArray = [
            'urn:magento:mftf:DataGenerator/etc/dataOperation.xsd' =>
                $this->getResourcePath('DataGenerator/etc/dataOperation.xsd'),
            'urn:magento:mftf:DataGenerator/etc/dataProfileSchema.xsd' =>
                $this->getResourcePath('DataGenerator/etc/dataProfileSchema.xsd'),
            'urn:magento:mftf:Page/etc/PageObject.xsd' =>
                $this->getResourcePath('Page/etc/PageObject.xsd'),
            'urn:magento:mftf:Page/etc/SectionObject.xsd' =>
                $this->getResourcePath('Page/etc/SectionObject.xsd'),
            'urn:magento:mftf:Test/etc/actionGroupSchema.xsd' =>
                $this->getResourcePath('Test/etc/actionGroupSchema.xsd'),
            'urn:magento:mftf:Test/etc/testSchema.xsd' =>
                $this->getResourcePath('Test/etc/testSchema.xsd'),
            'urn:magento:mftf:Suite/etc/suiteSchema.xsd' =>
                $this->getResourcePath('Suite/etc/suiteSchema.xsd')
        ];
        return $resourcesArray;
    }

    /**
     * Returns path (full or PhpStorm project-based) to XSD file
     *
     * @param $relativePath
     * @return string
     * @throws TestFrameworkException
     */
    private function getResourcePath($relativePath)
    {
        $urnPath = realpath(FilePathFormatter::format(FW_BP) . self::MFTF_SRC_PATH . $relativePath);
        $projectRoot = $this->getProjectRootPath();

        if ($projectRoot !== null) {
            return str_replace($projectRoot, self::PROJECT_PATH_IDENTIFIER, $urnPath);
        }

        return $urnPath;
    }

    /**
     * Returns Project root directory absolute path
     * @TODO Find out how to detect other types of installation
     *
     * @return string|null
     */
    private function getProjectRootPath()
    {
        $frameworkRoot = realpath(__DIR__);

        if ($this->isInstalledByComposer($frameworkRoot)) {
            return strstr($frameworkRoot, '/vendor/', true);
        }

        return null;
    }

    /**
     * Determines whether MFTF was installed using Composer
     *
     * @param string $frameworkRoot
     * @return bool
     */
    private function isInstalledByComposer($frameworkRoot)
    {
        return false !== strpos($frameworkRoot, '/vendor/');
    }
}
