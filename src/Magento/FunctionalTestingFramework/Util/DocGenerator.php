<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupAnnotationExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupObjectExtractor;

/**
 * Class TestGenerator
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
    # This is the only place FILENAMES is defined as this string
    const FILENAMES = "filenames";

    /**
     * DocGenerator constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * This creates html documentation for objects passed in
     *
     * @param ActionGroupObject[]|TestObject[] $annotatedObjects
     * @param string                           $outputDir
     * @return void
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
            mkdir($fullPath, 0755, true);
        }
        if (file_exists($filePath) and !$clean) {
            throw new TestFrameworkException(
                "$filePath already exists, please add --clean if you want to overwrite it."
            );
        }
        $pageGroups = [];

        foreach ($annotatedObjects as $name => $object) {
            $annotations = $object->getAnnotations();
            $filenames = explode(',', $object->getFilename());
            $arguments = $object->getArguments();

            $info = [
                actionGroupObject::ACTION_GROUP_DESCRIPTION => $annotations[actionGroupObject::ACTION_GROUP_DESCRIPTION]
                    ?? 'NO_DESCRIPTION_SPECIFIED',
                self::FILENAMES => $filenames,
                ActionGroupObjectExtractor::ACTION_GROUP_ARGUMENTS => $arguments
            ];

            $pageGroups = array_merge_recursive(
                $pageGroups,
                [$annotations[ActionGroupObject::ACTION_GROUP_PAGE] ?? 'NO_PAGE_SPECIFIED' => [$name => $info]]
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
     * @param array $annotationList
     * @return string
     */
    private function transformToMarkdown($annotationList)
    {
        $markdown = "#Action Group Information" . PHP_EOL;
        $markdown .= "This documentation contains a list of all Action Groups." .
            PHP_EOL .
            PHP_EOL;

        $markdown .= "---" . PHP_EOL;
        foreach ($annotationList as $group => $objects) {
            foreach ($objects as $name => $annotations) {
                $markdown .= "###$name" . PHP_EOL;
                $markdown .= "**Description**:" . PHP_EOL;
                $markdown .= "- " . $annotations[actionGroupObject::ACTION_GROUP_DESCRIPTION] . PHP_EOL . PHP_EOL;
                if (!empty($annotations[ActionGroupObjectExtractor::ACTION_GROUP_ARGUMENTS])) {
                    $markdown .= "**Action Group Arguments**:" . PHP_EOL . PHP_EOL;
                    $markdown .= "| Name | Type |" . PHP_EOL;
                    $markdown .= "| ---- | ---- |" . PHP_EOL;
                    foreach ($annotations[ActionGroupObjectExtractor::ACTION_GROUP_ARGUMENTS] as $argument) {
                        $argumentName = $argument->getName();
                        $argumentType = $argument->getDataType();
                        $markdown .= "| $argumentName | $argumentType |" . PHP_EOL;
                    }
                    $markdown .= PHP_EOL;
                }
                $markdown .= "**Located In**:" . PHP_EOL;
                foreach ($annotations[self::FILENAMES] as $filename) {
                    $relativeFilename = str_replace(MAGENTO_BP . DIRECTORY_SEPARATOR, "", $filename);
                    $markdown .= PHP_EOL .  "- $relativeFilename";
                }
                $markdown .= PHP_EOL . "***" . PHP_EOL;
            }
        }
        return $markdown;
    }
}
