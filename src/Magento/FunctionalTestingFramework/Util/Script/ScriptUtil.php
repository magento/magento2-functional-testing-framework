<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\Script;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Symfony\Component\Finder\Finder;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\TestGenerator;

/**
 * ScriptUtil class that contains helper functions for static and upgrade scripts
 *
 * @package Magento\FunctionalTestingFramework\Util\Script
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ScriptUtil
{
    const ACTIONGROUP_ARGUMENT_REGEX_PATTERN = '/<argument[^\/>]*name="([^"\']*)/';

    /**
     * Return all installed Magento module paths
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function getAllModulePaths()
    {
        MftfApplicationConfig::create(
            true,
            MftfApplicationConfig::UNIT_TEST_PHASE,
            false,
            MftfApplicationConfig::LEVEL_DEFAULT,
            true
        );

        return ModuleResolver::getInstance()->getModulesPath();
    }

    /**
     * Prints out given errors to file, and returns summary result string
     * @param array  $errors
     * @param string $filename
     * @param string $message
     * @return string
     */
    public function printErrorsToFile($errors, $filename, $message)
    {
        if (empty($errors)) {
            return $message . ": No errors found.";
        }

        $outputPath = getcwd() . DIRECTORY_SEPARATOR . $filename . ".txt";
        $fileResource = fopen($outputPath, 'w');

        foreach ($errors as $test => $error) {
            fwrite($fileResource, $error[0] . PHP_EOL);
        }

        fclose($fileResource);
        $errorCount = count($errors);
        $output = $message . ": Errors found across {$errorCount} file(s). Error details output to {$outputPath}";

        return $output;
    }

    /**
     * Return all XML files for $scope in given module paths, empty array if no path is valid
     *
     * @param array  $modulePaths
     * @param string $scope
     * @return Finder|array
     */
    public function getModuleXmlFilesByScope($modulePaths, $scope)
    {
        $found = false;
        $scopePath = DIRECTORY_SEPARATOR . ucfirst($scope) . DIRECTORY_SEPARATOR;
        $finder = new Finder();

        foreach ($modulePaths as $modulePath) {
            if (!realpath($modulePath . $scopePath)) {
                continue;
            }
            $finder->files()->followLinks()->in($modulePath . $scopePath)->name("*.xml");
            $found = true;
        }
        return $found ? $finder->files() : [];
    }

    /**
     * Resolve entity reference in {{entity.field}} or {{entity.field('param')}}
     *
     * @param array  $braceReferences
     * @param string $contents
     * @return array
     * @throws XmlException
     */
    public function resolveEntityReferences($braceReferences, $contents)
    {
        $entities = [];
        foreach ($braceReferences as $reference) {
            // trim `{{data.field}}` to `data`
            preg_match('/{{([^.]+)/', $reference, $entityName);
            // Double check that {{data.field}} isn't an argument for an ActionGroup
            $entity = $this->findEntity($entityName[1]);
            preg_match_all(self::ACTIONGROUP_ARGUMENT_REGEX_PATTERN, $contents, $possibleArgument);
            if (array_search($entityName[1], $possibleArgument[1]) !== false) {
                continue;
            }
            if ($entity !== null) {
                $entities[$entity->getName()] = $entity;
            }
        }
        return $entities;
    }

    /**
     * Drill down into params in {{ref.params('string', $data.key$, entity.reference)}} to resolve entity reference
     *
     * @param array  $braceReferences
     * @param string $contents
     * @return array
     * @throws XmlException
     */
    public function resolveParametrizedReferences($braceReferences, $contents)
    {
        $entities = [];
        foreach ($braceReferences as $parameterizedReference) {
            preg_match(
                ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER,
                $parameterizedReference,
                $arguments
            );
            $splitArguments = explode(',', ltrim(rtrim($arguments[0], ")"), "("));
            foreach ($splitArguments as $argument) {
                // Do nothing for 'string' or $persisted.data$
                if (preg_match(ActionObject::STRING_PARAMETER_REGEX, $argument)) {
                    continue;
                } elseif (preg_match(TestGenerator::PERSISTED_OBJECT_NOTATION_REGEX, $argument)) {
                    continue;
                }
                // trim `data.field` to `data`
                preg_match('/([^.]+)/', $argument, $entityName);
                // Double check that {{data.field}} isn't an argument for an ActionGroup
                $entity = $this->findEntity($entityName[1]);
                preg_match_all(self::ACTIONGROUP_ARGUMENT_REGEX_PATTERN, $contents, $possibleArgument);
                if (array_search($entityName[1], $possibleArgument[1]) !== false) {
                    continue;
                }
                if ($entity !== null) {
                    $entities[$entity->getName()] = $entity;
                }
            }
        }
        return $entities;
    }

    /**
     * Resolve entity by names
     *
     * @param array $references
     * @return array
     * @throws XmlException
     */
    public function resolveEntityByNames($references)
    {
        $entities = [];
        foreach ($references as $reference) {
            $entity = $this->findEntity($reference);
            if ($entity !== null) {
                $entities[$entity->getName()] = $entity;
            }
        }
        return $entities;
    }

    /**
     * Attempts to find any MFTF entity by its name. Returns null if none are found
     *
     * @param string $name
     * @return mixed
     * @throws XmlException
     */
    public function findEntity($name)
    {
        if ($name == '_ENV' || $name == '_CREDS') {
            return null;
        }

        if (DataObjectHandler::getInstance()->getObject($name)) {
            return DataObjectHandler::getInstance()->getObject($name);
        } elseif (PageObjectHandler::getInstance()->getObject($name)) {
            return PageObjectHandler::getInstance()->getObject($name);
        } elseif (SectionObjectHandler::getInstance()->getObject($name)) {
            return SectionObjectHandler::getInstance()->getObject($name);
        } elseif (ActionGroupObjectHandler::getInstance()->getObject($name)) {
            return ActionGroupObjectHandler::getInstance()->getObject($name);
        }

        try {
            return TestObjectHandler::getInstance()->getObject($name);
        } catch (TestReferenceException $e) {
        }
        return null;
    }
}
