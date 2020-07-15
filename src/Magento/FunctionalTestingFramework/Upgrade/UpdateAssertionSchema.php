<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Magento\FunctionalTestingFramework\Util\Script\ScriptUtil;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class UpdateAssertionSchema
 * @package Magento\FunctionalTestingFramework\Upgrade
 */
class UpdateAssertionSchema implements UpgradeInterface
{
    /**
     * Upgrades all test xml files, changing as many <assert> actions to be nested as possible
     * WILL NOT CATCH cases where style is a mix of old and new
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

        $testsUpdated = 0;
        foreach ($testPaths as $testsPath) {
            $finder = new Finder();
            $finder->files()->in($testsPath)->name("*.xml");

            $fileSystem = new Filesystem();
            foreach ($finder->files() as $file) {
                $contents = $file->getContents();
                // Isolate <assert ... /> but never <assert> ... </assert>, stops after finding first />
                preg_match_all('/<assert.*\/>/', $contents, $potentialAssertions);
                $newAssertions = [];
                $index = 0;
                if (empty($potentialAssertions[0])) {
                    continue;
                }
                foreach ($potentialAssertions[0] as $potentialAssertion) {
                    $newAssertions[$index] = $this->convertOldAssertionToNew($potentialAssertion);
                    $index++;
                }
                foreach ($newAssertions as $currentIndex => $replacements) {
                    $contents = str_replace($potentialAssertions[0][$currentIndex], $replacements, $contents);
                }
                $fileSystem->dumpFile($file->getRealPath(), $contents);
                $testsUpdated++;
            }
        }

        return ("Assertion Syntax updated in {$testsUpdated} file(s).");
    }

    /**
     * Takes given string and attempts to convert it from single line to multi-line
     *
     * @param string $assertion
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function convertOldAssertionToNew($assertion)
    {
        // <assertSomething => assertSomething
        $assertType = ltrim(explode(' ', $assertion)[0], '<');

        // regex to all attribute=>value pairs
        $allAttributes = "stepKey|actual|actualType|expected|expectedType|expectedValue|";
        $allAttributes .= "delta|message|selector|attribute|before|after|remove";
        $grabValueRegex = '/('. $allAttributes .')=(\'[^\']*\'|"[^"]*")/';

        // Makes 3 arrays in $grabbedParts:
        // 0 contains stepKey="value"
        // 1 contains stepKey
        // 2 contains value
        $sortedParts = [];
        preg_match_all($grabValueRegex, $assertion, $grabbedParts);
        for ($i = 0; $i < count($grabbedParts[0]); $i++) {
            $sortedParts[$grabbedParts[1][$i]] = $grabbedParts[2][$i];
        }

        // Begin trimming values and adding back into new string
        $trimmedParts = [];
        $newString = "<$assertType";
        $subElements = ["actual" => [], "expected" => []];
        foreach ($sortedParts as $type => $value) {
            // If attribute="'value'", elseif attribute='"value"', new nested format will break if we leave these in
            if (strpos($value, '"') === 0) {
                $value = rtrim(ltrim($value, '"'), '"');
            } elseif (strpos($value, "'") === 0) {
                $value = rtrim(ltrim($value, "'"), "'");
            }
            // If value is empty string (" " or ' '), trim again to become empty
            if (str_replace(" ", "", $value) == "''") {
                $value = "";
            } elseif (str_replace(" ", "", $value) == '""') {
                $value = "";
            }

            // Value is ready for storage/reapply
            $trimmedParts[$type] = $value;
            if (in_array($type, ["stepKey", "delta", "message", "before", "after", "remove"])) {
                // Add back as attribute safely
                $newString .= " $type=\"$value\"";
                continue;
            }

            // Store in subtype for child element creation
            if ($type == "actual") {
                $subElements["actual"]["value"] = $value;
            } elseif ($type == "actualType") {
                $subElements["actual"]["type"] = $value;
            } elseif ($type == "expected" or $type == "expectedValue") {
                $subElements["expected"]["value"] = $value;
            } elseif ($type == "expectedType") {
                $subElements["expected"]["type"] = $value;
            }
        }
        $newString .= ">\n";

        // Assert type is very edge-cased, completely different schema
        if ($assertType == 'assertElementContainsAttribute') {
            // assertElementContainsAttribute type defaulted to string if not present
            if (!isset($subElements["expected"]['type'])) {
                $subElements["expected"]['type'] = "string";
            }
            $value = $subElements['expected']['value'] ?? "";
            $type = $subElements["expected"]['type'];
            $selector = $trimmedParts['selector'];
            $attribute = $trimmedParts['attribute'];
            // @codingStandardsIgnoreStart
            $newString .= "\t\t\t<expectedResult selector=\"$selector\" attribute=\"$attribute\" type=\"$type\">$value</expectedResult>\n";
            // @codingStandardsIgnoreEnd
        } else {
            // Set type to const if it's absent, old default
            if (isset($subElements["actual"]['value']) && !isset($subElements["actual"]['type'])) {
                $subElements["actual"]['type'] = "const";
            }
            if (isset($subElements["expected"]['value']) && !isset($subElements["expected"]['type'])) {
                $subElements["expected"]['type'] = "const";
            }
            foreach ($subElements as $type => $subElement) {
                if (empty($subElement)) {
                    continue;
                }
                $value = $subElement['value'];
                $typeValue = $subElement['type'];
                $newString .= "\t\t\t<{$type}Result type=\"$typeValue\">$value</{$type}Result>\n";
            }
        }
        $newString .= "        </$assertType>";
        return $newString;
    }
}
