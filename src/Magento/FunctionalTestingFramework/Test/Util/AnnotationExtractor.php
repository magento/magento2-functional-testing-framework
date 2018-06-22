<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;

/**
 * Class AnnotationExtractor
 */
class AnnotationExtractor extends BaseObjectExtractor
{
    /**
     * Mappings of all Test => title mappings, indexed by Story
     * e.g. $storyToTitleMappings['storyAnnotation'] = ['testName' => 'titleAnnotation']
     * @var array
     */
    private $storyToTitleMappings = [];

    const ANNOTATION_VALUE = 'value';
    const MAGENTO_TO_ALLURE_SEVERITY_MAP = [
        "BLOCKER" => "BLOCKER",
        "CRITICAL" => "CRITICAL",
        "MAJOR" => "NORMAL",
        "AVERAGE" => "MINOR",
        "MINOR" => "TRIVIAL"
    ];

    /**
     * AnnotationExtractor constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * This method trims away irrelevant tags and returns annotations used in the array passed. The annotations
     * can be found in both Tests and their child element tests.
     *
     * @param array $testAnnotations
     * @param string $filename
     * @return array
     * @throws XmlException
     */
    public function extractAnnotations($testAnnotations, $filename)
    {
        $annotationObjects = [];
        $annotations = $this->stripDescriptorTags($testAnnotations, self::NODE_NAME);

        // parse the Test annotations

        foreach ($annotations as $annotationKey => $annotationData) {
            $annotationValues = [];
            // Only transform severity annotation
            if ($annotationKey == "severity") {
                $annotationObjects[$annotationKey] = $this->transformAllureSeverityToMagento(
                    $annotationData[0]['value']
                );
                continue;
            }

            if ($annotationKey == "skip") {
                $annotationData = $annotationData['issueId'];
                $this->validateSkippedIssues($annotationData, $filename);
            }

            foreach ($annotationData as $annotationValue) {
                $annotationValues[] = $annotationValue[self::ANNOTATION_VALUE];
            }
            // TODO deprecation|deprecate MFTF 3.0.0
            if ($annotationKey == "group" && in_array("skip", $annotationValues)) {
                print(
                    "Test: $filename" .
                    PHP_EOL .
                    "Use of group skip will be deprecated in MFTF 3.0.0. Please update tests to use skip tags." .
                    PHP_EOL
                );
            }

            $annotationObjects[$annotationKey] = $annotationValues;
        }
        $this->addStoryTitleToMap($annotationObjects, $filename);
        return $annotationObjects;
    }

    /**
     * Adds story/title/filename combination to static map
     * @param array $annotations
     * @param string $filename
     * @return void
     */
    public function addStoryTitleToMap($annotations, $filename)
    {
        if (isset($annotations['stories']) && isset($annotations['title'])) {
            $story = $annotations['stories'][0];
            $title = $annotations['title'][0];
            $this->storyToTitleMappings[$story . "/" . $title][] = $filename;
        }
    }

    /**
     * Validates that all Story/Title combinations are unique, builds list of violators if found.
     * @throws XmlException
     * @return void
     */
    public function validateStoryTitleUniqueness()
    {
        $dupes = [];

        foreach ($this->storyToTitleMappings as $storyTitle => $files) {
            if (count($files) > 1) {
                $dupes[$storyTitle] = "'" . implode("', '", $files) . "'";
            }
        }
        if (!empty($dupes)) {
            $message = "Story and Title annotation pairs must be unique:\n\n";
            foreach ($dupes as $storyTitle => $tests) {
                $storyTitleArray = explode("/", $storyTitle);
                $story = $storyTitleArray[0];
                $title = $storyTitleArray[1];
                $message .= "Story: '{$story}' Title: '{$title}' in Tests {$tests}\n\n";

            }
            throw new XmlException($message);
        }
    }

    /**
     * Validates that all issueId tags contain a non-empty value
     * @param array $issues
     * @param string $filename
     * @throws XmlException
     * @return void
     */
    public function validateSkippedIssues($issues, $filename)
    {
        foreach ($issues as $issueId)
        if (empty($issueId['value'])) {
            $message = "issueId for skipped tests cannot be empty. Test: $filename";
            throw new XmlException($message);
        }
    }

    /**
     * This method transforms Magento severity values from Severity annotation
     * Returns Allure annotation value
     *
     * @param string $annotationData
     * @return array
     */
    public function transformAllureSeverityToMagento($annotationData)
    {
        $annotationValue = strtoupper($annotationData);
        //Mapping Magento severity to Allure Severity
        //Attempts to resolve annotationValue reference against MAGENTO_TO_ALLURE_SEVERITY_MAP -
        // if not found returns without modification
        $allureAnnotation[] = self::MAGENTO_TO_ALLURE_SEVERITY_MAP[$annotationValue] ?? $annotationValue;

        return $allureAnnotation;
    }
}
