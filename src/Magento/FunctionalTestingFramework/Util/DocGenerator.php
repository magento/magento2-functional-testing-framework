<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\PersistedObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Suite\Handlers\SuiteObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Manifest\BaseTestManifest;
use Magento\FunctionalTestingFramework\Util\Manifest\TestManifestFactory;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;

/**
 * Class TestGenerator
 * @SuppressWarnings(PHPMD)
 */
class DocGenerator
{
    const DEFAULT_OUTPUT_DIR =
        PROJECT_ROOT .
        DIRECTORY_SEPARATOR .
        "dev" .
        DIRECTORY_SEPARATOR .
        "tests" .
        DIRECTORY_SEPARATOR .
        "docs";
    const DOC_NAME = "documentation.md";
    const ANNOTATION_MODULE = "";
    const ANNOTATION_PAGE = "page";
    const ANNOTATION_DESCRIPTION = "description";

    /**
     * Single instance of class var
     *
     * @var ActionGroupObjectHandler
     */
    private static $instance;

    /**
     * Array of action groups
     *
     * @var ActionGroupObjectHandler
     */
    private $actionGroups;

    /**
     * Singleton getter for instance of ActionGroupObjectHandler
     *
     * @return ActionGroupObjectHandler
     */
    public static function getInstance(): DocGenerator
    {
        if (!self::$instance) {
            self::$instance = new DocGenerator();
        }

        return self::$instance;
    }

    /**
     * DocGenerator constructor.
     *
     * @param array    $actionGroups
     */
    private function __construct()
    {
        // private constructor for factory
    }

    /**
     * This creates html documentation for objects passed in
     *
     * @param ActionGroupObject[]|TestObject[]     $annotatedObjects
     * @param string                               $outputDir
     * @throws TestFrameworkException
     */
    public function createDocumentation($annotatedObjects, $outputDir, $clean)
    {
        if (empty($outputDir)) {
            $fullPath = self::DEFAULT_OUTPUT_DIR . DIRECTORY_SEPARATOR;
        } else {
            $fullPath = $outputDir . DIRECTORY_SEPARATOR;
        }
        $filePath = $fullPath . self::DOC_NAME;

        if (!file_exists($fullPath)) {
            mkdir(dirname($fullPath), 0755, true);
        }
        if (file_exists($filePath) and !$clean) {
            throw new TestFrameworkException(
                "$filePath already exists, please add --clean if you want to overwrite it."
            );
        }
        $pageGroups = [];

        foreach ($annotatedObjects as $name => $object) {
            $annotations = $object->getAnnotations();
            $filenames = $this->flattenArray($object->getFileNames());

            $info = [
                self::ANNOTATION_DESCRIPTION => $annotations[self::ANNOTATION_DESCRIPTION] ?? 'NO_DESCRIPTION_SPECIFIED',
                'filenames' => $filenames
                ];
            $pageGroups = array_merge_recursive(
                $pageGroups,
                [$annotations[self::ANNOTATION_PAGE] ?? 'NO_PAGE_SPECIFIED' => [$name => $info]]
            );
        }

        ksort($pageGroups);
        foreach ($pageGroups as $page => $groups) {
            ksort($groups);
            $pageGroups[$page] = $groups;
        }

        $markdown = $this->transformToMarkdown($pageGroups);



        file_put_contents($filePath, $markdown);
    }

    /**
     * This creates html documentation for objects passed in
     *
     * @param array     $annotationList
     * @return string
     */
    private function transformToMarkdown($annotationList)
    {
        $markdown = "";

        foreach($annotationList as $group => $objects)
        {
            $markdown .= "###$group" . PHP_EOL . PHP_EOL;
            foreach($objects as $name => $annotations)
            {
                $markdown .= "####$name" . PHP_EOL;
                $markdown .= $annotations[self::ANNOTATION_DESCRIPTION] . PHP_EOL . PHP_EOL;
                $markdown .= "Located in:" . PHP_EOL;
                foreach($annotations['filenames'] as $filename)
                {
                    $markdown .= "- $filename";
                }
                $markdown .= PHP_EOL . PHP_EOL;
            }
        }
        return $markdown;
    }

    /**
     * Flattens array to one level
     *
     * @param array     $uprightArray
     * @return array
     */
    private function flattenArray($uprightArray)
    {
        $flatArray = [];

        foreach($uprightArray as $value) {
            if(is_array($value)) {
                array_merge($flatArray, $this->flattenArray($value));
            } else {
                array_push($flatArray, $value);
            }
        }

        return $flatArray;
    }

}