<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Symfony\Component\Console\Input\InputInterface;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Symfony\Component\Finder\Finder;
use Exception;

/**
 * Class ActionGroupArgumentsCheck
 * @package Magento\FunctionalTestingFramework\StaticCheck
 */
class ActionGroupArgumentsCheck implements StaticCheckInterface
{

    const ACTIONGROUP_XML_REGEX_PATTERN = '/<actionGroup\sname=(?: (?!<\/actionGroup>).)*/mxs';
    const ACTIONGROUP_ARGUMENT_REGEX_PATTERN = '/<argument[^\/>]*name="([^"\']*)/mxs';
    const ACTIONGROUP_NAME_REGEX_PATTERN = '/<actionGroup name=["\']([^\'"]*)/';

    const ERROR_LOG_FILENAME = 'mftf-arguments-checks';
    const ERROR_LOG_MESSAGE = 'MFTF Action Group Unused Arguments Check';

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
     * @throws Exception
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

        $allModules = ModuleResolver::getInstance()->getModulesPath();

        $actionGroupXmlFiles = StaticCheckHelper::buildFileList(
            $allModules,
            DIRECTORY_SEPARATOR . 'ActionGroup' . DIRECTORY_SEPARATOR
        );

        $this->errors = $this->findErrorsInFileSet($actionGroupXmlFiles);

        $this->output = StaticCheckHelper::printErrorsToFile(
            $this->errors,
            self::ERROR_LOG_FILENAME,
            self::ERROR_LOG_MESSAGE
        );
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
     * Finds all unused arguments in given set of actionGroup files
     * @param Finder $files
     * @return array $testErrors
     */
    private function findErrorsInFileSet($files)
    {
        $actionGroupErrors = [];
        foreach ($files as $filePath) {
            $contents = file_get_contents($filePath);
            preg_match_all(self::ACTIONGROUP_XML_REGEX_PATTERN, $contents, $actionGroups);
            $actionGroupToArguments = $this->buildUnusedArgumentList($actionGroups[0]);
            $actionGroupErrors += $this->setErrorOutput($actionGroupToArguments, $filePath);
        }
        return $actionGroupErrors;
    }

    /**
     * Builds array of action group => unused arguments
     * @param array $actionGroups
     * @return array $actionGroupToArguments
     */
    private function buildUnusedArgumentList($actionGroups)
    {
        $actionGroupToArguments = [];

        foreach ($actionGroups as $actionGroupXml) {
            preg_match(self::ACTIONGROUP_NAME_REGEX_PATTERN, $actionGroupXml, $actionGroupName);
            $unusedArguments = $this->findUnusedArguments($actionGroupXml);
            if (!empty($unusedArguments)) {
                $actionGroupToArguments[$actionGroupName[1]] = $unusedArguments;
            }
        }
        return $actionGroupToArguments;
    }

    /**
     * Returns unused arguments in an action group
     * @param string $actionGroupXml
     * @return array
     */
    private function findUnusedArguments($actionGroupXml)
    {
        $unusedArguments = [];

        preg_match_all(self::ACTIONGROUP_ARGUMENT_REGEX_PATTERN, $actionGroupXml, $arguments);
        preg_match(self::ACTIONGROUP_NAME_REGEX_PATTERN, $actionGroupXml, $actionGroupName);
        try {
            $actionGroup = ActionGroupObjectHandler::getInstance()->getObject($actionGroupName[1]);
        } catch (XmlException $e) {
        }
        foreach ($arguments[1] as $argument) {
            //pattern to match all argument references
            $patterns = [
                '(\{{2}' . $argument . '(\.[a-zA-Z0-9_\[\]\(\).,\'\/ ]+)?}{2})',
                '([(,\s\'$$]' . $argument . '(\.[a-zA-Z0-9_$\[\]]+)?[),\s\'])'
            ];
            // matches entity references
            if (preg_match($patterns[0], $actionGroupXml)) {
                continue;
            }
            //matches parametrized references
            if (preg_match($patterns[1], $actionGroupXml)) {
                continue;
            }
            //for extending action groups, exclude arguments that are also defined in parent action group
            if ($this->isParentActionGroupArgument($argument, $actionGroup)) {
                continue;
            }
            $unusedArguments[] = $argument;
        }
        return $unusedArguments;
    }

    /**
     * Checks if the argument is also defined in the parent for extending action groups.
     * @param string            $argument
     * @param ActionGroupObject $actionGroup
     * @return boolean
     */
    private function isParentActionGroupArgument($argument, $actionGroup)
    {
        $parentActionGroupName = $actionGroup->getParentName();
        if ($parentActionGroupName !== null) {
            $parentActionGroup = ActionGroupObjectHandler::getInstance()->getObject($parentActionGroupName);
            $parentArguments = $parentActionGroup->getArguments();
            foreach ($parentArguments as $parentArgument) {
                if ($argument === $parentArgument->getName()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Builds and returns error output for violating references
     *
     * @param array  $actionGroupToArguments
     * @param string $path
     * @return mixed
     */
    private function setErrorOutput($actionGroupToArguments, $path)
    {
        $actionGroupErrors = [];
        if (!empty($actionGroupToArguments)) {
            // Build error output
            $errorOutput = "\nFile \"{$path->getRealPath()}\"";
            $errorOutput .= "\ncontains action group(s) with unused arguments.\n\t\t";
            foreach ($actionGroupToArguments as $actionGroup => $arguments) {
                $errorOutput .= "\n\t {$actionGroup} has unused argument(s): " . implode(", ", $arguments);
            }
            $actionGroupErrors[$path->getRealPath()][] = $errorOutput;
        }
        return $actionGroupErrors;
    }
}
