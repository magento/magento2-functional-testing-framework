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
use Symfony\Component\Finder\Finder;

/**
 * Class SplitMultipleEntitiesFiles
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class SplitMultipleEntitiesFiles implements UpgradeInterface
{
    const XML_VERSION = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    const XML_COPYRIGHT = '<!--' . PHP_EOL
        . ' /**' . PHP_EOL
        . '  * Copyright © Magento, Inc. All rights reserved.' . PHP_EOL
        . '  * See COPYING.txt for license details.' . PHP_EOL
        . '  */' . PHP_EOL
        . '-->' . PHP_EOL;
    const XML_NAMESPACE = 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . PHP_EOL;
    const XML_SCHEMA_LOCATION = "\t" . 'xsi:noNamespaceSchemaLocation="urn:magento:mftf:';

    const FILENAME_BASE = 'base';
    const FILENAME_SUFFIX = 'type';

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
     * Entity categories for the upgrade script
     *
     * @var array
     */
    private $entityCategories = [
        'Suite' => 'Suite/etc/suiteSchema.xsd',
        'Test' => 'Test/etc/testSchema.xsd',
        'ActionGroup' => 'Test/etc/actionGroupSchema.xsd',
        'Page' => 'Page/etc/PageObject.xsd',
        'Section' => 'Page/etc/SectionObject.xsd',
    ];

    /**
     * Scan all xml files and split xml files that contains more than one entities
     * for Test, Action Group, Page, Section, Suite types.
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
        foreach ($this->entityCategories as $type => $urn) {
            $xmlFiles = $scriptUtil->getModuleXmlFilesByScope($testPaths, $type);
            $this->processXmlFiles($xmlFiles, $type, $urn);
        }

        return ("Split multiple entities in {$this->testsUpdated} file(s).");
    }

    /**
     * Split on list of xml files
     *
     * @param Finder $xmlFiles
     * @param string $type
     * @param string $urn
     * @return void
     */
    private function processXmlFiles($xmlFiles, $type, $urn)
    {
        foreach ($xmlFiles as $file) {
            $contents = $file->getContents();
            $domDocument = new \DOMDocument();
            $domDocument->loadXML($contents);
            $entities = $domDocument->getElementsByTagName(lcfirst($type));

            if ($entities->length > 1) {
                $filename = $file->getRealPath();
                if ($this->output->isVerbose()) {
                    $this->output->writeln('Processing file:' . $filename);
                }
                foreach ($entities as $entity) {
                    /** @var \DOMElement $entity */
                    $entityName = $entity->getAttribute('name');
                    $entityContent = $entity->ownerDocument->saveXML($entity);

                    $dir = dirname($file);
                    $dir .= DIRECTORY_SEPARATOR . ucfirst(basename($file, '.xml'));
                    $splitFileName = $this->formatName($entityName, $type);
                    $this->filePutContents(
                        $dir . DIRECTORY_SEPARATOR . $splitFileName . '.xml',
                        $type,
                        $urn,
                        $entityContent
                    );
                    if ($this->output->isVerbose()) {
                        $this->output->writeln(
                            'Created file:' . $dir . DIRECTORY_SEPARATOR . $splitFileName . '.xml'
                        );
                    }
                    $this->testsUpdated++;
                }
                unlink($file);
                if ($this->output->isVerbose()) {
                    $this->output->writeln('Unlinked file:' . $filename . PHP_EOL);
                }
            }
        }
    }

    /**
     * Create file with contents and create dir if needed
     *
     * @param string $fullPath
     * @param string $type
     * @param string $urn
     * @param string $contents
     * @return void
     */
    private function filePutContents($fullPath, $type, $urn, $contents)
    {
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Make sure not overwriting an existing file
        $fullPath = $this->getNonExistingFileFullPath($fullPath, $type);

        $fullContents = self::XML_VERSION
            . self::XML_COPYRIGHT
            . '<' . lcfirst($type) . 's '
            . self::XML_NAMESPACE
            . self::XML_SCHEMA_LOCATION . $urn . '">' . PHP_EOL
            . '    ' . $contents . PHP_EOL
            . '</' . lcfirst($type) . 's>' . PHP_EOL;

        file_put_contents($fullPath, $fullContents);
    }

    /**
     * Format name to include type if it's Page, Section or Action Group
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    private function formatName($name, $type)
    {
        $name = ucfirst($name);
        $type = ucfirst($type);

        if ($type !== 'Section' && $type !== 'Page' && $type !== 'ActionGroup') {
            return $name;
        }

        $parts = $this->getFileNameParts($name, $type);
        if (empty($parts[self::FILENAME_SUFFIX])) {
            $name .= $type;
        }
        return $name;
    }

    /**
     * Vary the input to return a non-existing file name
     *
     * @param string $fullPath
     * @param string $type
     * @return string
     */
    private function getNonExistingFileFullPath($fullPath, $type)
    {
        $type = ucfirst($type);
        $dir = dirname($fullPath);
        $filename = basename($fullPath, '.xml');
        $i = 1;
        $parts = [];
        while (file_exists($fullPath)) {
            if (empty($parts)) {
                $parts = $this->getFileNameParts($filename, $type);
            }
            $basename = $parts[self::FILENAME_BASE] . strval(++$i);
            $fullPath = $dir . DIRECTORY_SEPARATOR . $basename . $parts[self::FILENAME_SUFFIX] . '.xml';
        }
        return $fullPath;
    }

    /**
     * Split filename into two parts and return it in an associate array with keys FILENAME_BASE and FILENAME_SUFFIX
     *
     * @param string $filename
     * @param string $type
     * @return array
     */
    private function getFileNameParts($filename, $type)
    {
        $type = ucfirst($type);
        $fileNameParts = [];
        if (substr($filename, -strlen($type)) === $type) {
            $fileNameParts[self::FILENAME_BASE] = substr($filename, 0, strlen($filename) - strlen($type));
            $fileNameParts[self::FILENAME_SUFFIX] = $type;
        } else {
            $fileNameParts[self::FILENAME_BASE] = $filename;
            $fileNameParts[self::FILENAME_SUFFIX] = '';
        }
        return $fileNameParts;
    }
}
