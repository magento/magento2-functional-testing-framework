<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Symfony\Component\Console\Input\InputInterface;
use Exception;

/**
 * Class UnusedArgumentsCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UnusedArgumentsCheck implements StaticCheckInterface
{

    /**
     * Array containing all errors found after running the execute() function.
     * @var array
     */
    private $errors = [];

    /**
     * String representing the output summary found after running the execute() function.
     * @var string
     */
    private $output;

    /**
     * Checks unused arguments in action groups and prints out error to file.
     *
     * @param  InputInterface $input
     * @return void
     * @throws Exception;
     */
    public function execute(InputInterface $input)
    {
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::UNIT_TEST_PHASE,
            false,
            MftfApplicationConfig::LEVEL_NONE,
            true
        );

        $actionGroups = ActionGroupObjectHandler::getInstance()->initActionGroups();

        $unusedArgumentList = $this->buildUnusedArgumentList($actionGroups);

        $this->errors += $this->setErrorOutput($unusedArgumentList);

        $this->output = $this->printErrorsToFile();
    }

    /**
     * Return array containing all errors found after running the execute() function.
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return string of a short human readable result of the check. For example: "No unused arguments found."
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Builds array of action groups => unused arguments
     * @param array $actionGroups
     * @return array $actionGroupToArguments
     */
    private function buildUnusedArgumentList($actionGroups)
    {
        $actionGroupToArguments = [];

        foreach ($actionGroups as $actionGroup) {
            $unusedArguments = $this->findUnusedArguments($actionGroup);
            if (!empty($unusedArguments)) {
                $actionGroupToArguments[$actionGroup->getFilename()][$actionGroup->getName()] = $unusedArguments;
            }
        }
        return $actionGroupToArguments;
    }

    /**
     * Returns unused arguments in an action group.
     * @param ActionGroupObject $actionGroup
     * @return array $unusedArguments
     */
    private function findUnusedArguments($actionGroup)
    {
        $unusedArguments = [];
        //extract all action attribute values
        $actionAttributeValues = $this->getAllActionAttributeValues($actionGroup);
        $argumentList = $actionGroup->getArguments();
        foreach ($argumentList as $argument) {
            $argumentName = $argument->getName();
            //pattern to match all argument references
            $pattern = '(.*[\W]+(?<!\.)' . $argumentName . '[\W]+.*)';
            if (preg_grep($pattern, $actionAttributeValues)) {
                continue;
            }
            $unusedArguments[] = $argument->getName();
        }
        return $unusedArguments;
    }

    /**
     * Returns array of all action attribute values in an action group.
     * @param $actionGroup
     * @return array
     */
    private function getAllActionAttributeValues($actionGroup)
    {
        $allAttributeValues = [];
        $actions = $actionGroup->getActions();
        foreach ($actions as $action) {
            $actionAttributeValues = $this->extractAttributeValues($action);
            $allAttributeValues = array_merge($allAttributeValues, $actionAttributeValues);
        }
        return array_unique($allAttributeValues);
    }

    /**
     * Builds and returns flattened attribute value list for an action.
     * @param ActionObject $action
     * @return array $flattenedAttributeValues
     */
    private function extractAttributeValues($action)
    {
        $flattenedAttributeValues = [];
        $actionAttributes = $action->getCustomActionAttributes();
        //check if action has nodes eg. expectedResult, actualResult and flatten array
        foreach ($actionAttributes as $attributeName => $attributeValue) {
            if (is_array($attributeValue)) {
                $flattenedAttributeValues = array_merge($flattenedAttributeValues, array_values($attributeValue));
            } else {
                $flattenedAttributeValues[] = $attributeValue;
            }
        }
        return $flattenedAttributeValues;
    }

    /**
     * Builds and returns error output for unused arguments
     *
     * @param array $unusedArgumentList
     * @return mixed
     */
    private function setErrorOutput($unusedArgumentList)
    {
        $testErrors = [];

        if (!empty($unusedArgumentList)) {
            // Build error output
            foreach ($unusedArgumentList as $path => $actionGroupToArguments) {
                $errorOutput = "\nFile \"{$path}\"";
                $errorOutput .= "\ncontains action group(s) with unused arguments.\n\t\t";

                foreach ($actionGroupToArguments as $actionGroup => $arguments) {
                    $errorOutput .= "\n\t {$actionGroup} has unused argument(s): " . implode(", ", $arguments);
                }
                $testErrors[$path][] = $errorOutput;
            }
        }
        return $testErrors;
    }

    /**
     * Prints out given errors to file, and returns summary result string
     * @return string
     */
    private function printErrorsToFile()
    {
        $errors = $this->getErrors();

        if (empty($errors)) {
            return "No unused arguments found.";
        }

        $outputPath = getcwd() . DIRECTORY_SEPARATOR . "mftf-arguments-checks.txt";
        $fileResource = fopen($outputPath, 'w');
        $header = "MFTF ActionGroup Arguments Check:\n";
        fwrite($fileResource, $header);

        foreach ($errors as $test => $error) {
            fwrite($fileResource, $error[0] . PHP_EOL);
        }

        fclose($fileResource);
        $errorCount = count($errors);
        $output = "Unused arguments found across {$errorCount} actionGroup(s). Error details output to {$outputPath}";

        return $output;
    }
}
