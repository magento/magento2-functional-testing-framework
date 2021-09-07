<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Util\GenerationDataReferenceResolver;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class EntityDataObject
 */
class EntityDataObject
{
    const NO_UNIQUE_PROCESS = 0;
    const SUITE_UNIQUE_VALUE = 1;
    const CEST_UNIQUE_VALUE = 2;
    const SUITE_UNIQUE_NOTATION = 3;
    const CEST_UNIQUE_NOTATION = 4;
    const SUITE_UNIQUE_FUNCTION = 'msqs';
    const CEST_UNIQUE_FUNCTION = 'msq';

    /**
     * Name of the entity
     *
     * @var string
     */
    private $name;

    /**
     * Type of the entity
     *
     * @var string
     */
    private $type;

    /**
     * An array of required entity name to corresponding type
     *
     * @var string[]
     */
    private $linkedEntities = [];

    /**
     * An array of variable mappings for static data
     *
     * @var string[]
     */
    private $vars;

    /**
     * An array of Data Name to Data Value
     *
     * @var string[]
     */
    private $data = [];

    /**
     * Array of data name and its uniqueness attribute value.
     *
     * @var string[]
     */
    private $uniquenessData = [];

    /**
     * String of parent Entity
     *
     * @var string
     */
    private $parentEntity;

    /**
     * String of filename
     * @var string
     */
    private $filename;

    /**
     * Deprecated message.
     *
     * @var string
     */
    private $deprecated;

    /**
     * Constructor
     *
     * @param string      $name
     * @param string      $type
     * @param string[]    $data
     * @param string[]    $linkedEntities
     * @param string[]    $uniquenessData
     * @param string[]    $vars
     * @param string      $parentEntity
     * @param string      $filename
     * @param string|null $deprecated
     */
    public function __construct(
        $name,
        $type,
        $data,
        $linkedEntities,
        $uniquenessData,
        $vars = [],
        $parentEntity = null,
        $filename = null,
        $deprecated = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->data = $data;
        $this->linkedEntities = $linkedEntities;
        if ($uniquenessData) {
            $this->uniquenessData = $uniquenessData;
        }

        $this->vars = $vars;
        $this->parentEntity = $parentEntity;
        $this->filename = $filename;
        $this->deprecated = $deprecated;
    }

    /**
     * Getter for the deprecated attr of the section.
     *
     * @return string
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Get the name of this entity data object
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the Entity Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the type of this entity data object
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for data array (field name to value)
     *
     * @return \string[]
     */
    public function getAllData()
    {
        return $this->data;
    }

    /**
     * Get a piece of data by name and the desired uniqueness format.
     *
     * @param string  $name
     * @param integer $uniquenessFormat
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getDataByName($name, $uniquenessFormat)
    {
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            LoggingUtil::getInstance()->getLogger(EntityDataObject::class)
                ->debug("Fetching data field from entity", ["entity" => $this->getName(), "field" => $name]);
        }

        if (!$this->isValidUniqueDataFormat($uniquenessFormat)) {
            $exceptionMessage = sprintf("Invalid unique data format value: %s \n", $uniquenessFormat);
            LoggingUtil::getInstance()->getLogger(EntityDataObject::class)
                ->error($exceptionMessage, ["entity" => $this->getName(), "field" => $name]);
            throw new TestFrameworkException($exceptionMessage);
        }

        if ($this->data === null) {
            return null;
        }
        return $this->resolveDataReferences($name, $uniquenessFormat);
    }

    /**
     * Resolves data references in entities while generating static test files.
     *
     * @param string  $name
     * @param integer $uniquenessFormat
     * @return string|null
     * @throws TestFrameworkException
     * @throws TestReferenceException
     */
    private function resolveDataReferences($name, $uniquenessFormat)
    {
        $name_lower = strtolower($name);
        $dataReferenceResolver = new GenerationDataReferenceResolver();
        if (array_key_exists($name_lower, $this->data)) {
            if (is_array($this->data[$name_lower])) {
                return $this->data[$name_lower];
            }
            $uniquenessData = $this->getUniquenessDataByName($name_lower) === null
                ? $dataReferenceResolver->getDataUniqueness(
                    $this->data[$name_lower],
                    $this->name . '.' . $name
                )
                : $this->getUniquenessDataByName($name_lower);
            if ($uniquenessData !== null) {
                $this->uniquenessData[$name] = $uniquenessData;
            }
            $this->data[$name_lower] = $dataReferenceResolver->getDataReference(
                $this->data[$name_lower],
                $this->name . '.' . $name
            );
            if (null === $uniquenessData || $uniquenessFormat === self::NO_UNIQUE_PROCESS) {
                return $this->data[$name_lower];
            }
            return $this->formatUniqueData($name_lower, $uniquenessData, $uniquenessFormat);
        } elseif (array_key_exists($name, $this->data)) {
            if (is_array($this->data[$name])) {
                return $this->data[$name];
            }
            $this->data[$name] = $dataReferenceResolver->getDataReference(
                $this->data[$name],
                $this->name . '.' . $name
            );
            // Data returned by the API may be camelCase so we need to check for the original $name also.
            return $this->data[$name];
        } else {
            return null;
        }
    }

    /**
     * Getter for data parent
     *
     * @return \string
     */
    public function getParentName()
    {
        return $this->parentEntity;
    }

    /**
     * Formats and returns data based on given uniqueDataFormat and prefix/suffix.
     *
     * @param string $name
     * @param string $uniqueData
     * @param string $uniqueDataFormat
     * @return null|string
     * @throws TestFrameworkException
     */
    private function formatUniqueData($name, $uniqueData, $uniqueDataFormat)
    {
        switch ($uniqueDataFormat) {
            case self::SUITE_UNIQUE_VALUE:
                $this->checkUniquenessFunctionExists(self::SUITE_UNIQUE_FUNCTION, $uniqueDataFormat);
                if ($uniqueData === 'prefix') {
                    return msqs($this->getName()) . $this->data[$name];
                } else { // $uniData == 'suffix'
                    return $this->data[$name] . msqs($this->getName());
                }
                break;
            case self::CEST_UNIQUE_VALUE:
                $this->checkUniquenessFunctionExists(self::CEST_UNIQUE_FUNCTION, $uniqueDataFormat);
                if ($uniqueData === 'prefix') {
                    return msq($this->getName()) . $this->data[$name];
                } else { // $uniqueData == 'suffix'
                    return $this->data[$name] . msq($this->getName());
                }
                break;
            case self::SUITE_UNIQUE_NOTATION:
                if ($uniqueData === 'prefix') {
                    return self::SUITE_UNIQUE_FUNCTION . '("' . $this->getName() . '")' . $this->data[$name];
                } else { // $uniqueData == 'suffix'
                    return $this->data[$name] . self::SUITE_UNIQUE_FUNCTION . '("' . $this->getName() . '")';
                }
                break;
            case self::CEST_UNIQUE_NOTATION:
                if ($uniqueData === 'prefix') {
                    return self::CEST_UNIQUE_FUNCTION . '("' . $this->getName() . '")' . $this->data[$name];
                } else { // $uniqueData == 'suffix'
                    return $this->data[$name] . self::CEST_UNIQUE_FUNCTION . '("' . $this->getName() . '")';
                }
                break;
            default:
                break;
        }
        return null;
    }

    /**
     * Performs a check that the given uniqueness function exists, throws an exception if it doesn't.
     *
     * @param string $function
     * @param string $uniqueDataFormat
     * @return void
     * @throws TestFrameworkException
     */
    private function checkUniquenessFunctionExists($function, $uniqueDataFormat)
    {
        if (!function_exists($function)) {
            $exceptionMessage = sprintf(
                'Unique data format value: %s can only be used when running cests.\n',
                $uniqueDataFormat
            );

            throw new TestFrameworkException($exceptionMessage);
        }
    }

    /**
     * Function which returns a reference to another entity (e.g. a var with entity="category" field="id" returns as
     * category->id)
     *
     * @param string $key
     * @return string|null
     */
    public function getVarReference($key)
    {
        if (array_key_exists($key, $this->vars)) {
            return $this->vars[$key];
        }

        return null;
    }

    /**
     * This function takes an array of entityTypes indexed by name and a string that represents the type of interest.
     * The function returns an array of entityNames relevant to the specified type.
     *
     * @param string $type
     * @return array
     */
    public function getLinkedEntitiesOfType($type)
    {
        $groupedArray = [];

        foreach ($this->linkedEntities as $entityName => $entityType) {
            if ($entityType === $type) {
                $groupedArray[] = $entityName;
            }
        }

        return $groupedArray;
    }

    /**
     * Get array of entity names specified as associated to this entity.
     *
     * @return \string[]
     */
    public function getLinkedEntities()
    {
        return $this->linkedEntities;
    }

    /**
     * Get array of var based fields defined in this entity.
     *
     * @return \string[]
     */
    public function getVarReferences()
    {
        return $this->vars;
    }

    /**
     * This function retrieves uniqueness data by its name.
     *
     * @param string $dataName
     * @return string|null
     */
    public function getUniquenessDataByName($dataName)
    {
        $name = strtolower($dataName);

        if (array_key_exists($name, $this->uniquenessData)) {
            return $this->uniquenessData[$name];
        }

        return null;
    }

    /**
     * This function retrieves uniqueness data.
     *
     * @return array|null
     */
    public function getUniquenessData()
    {
        return $this->uniquenessData;
    }

    /**
     * Validate if input value is a valid unique data format.
     *
     * @param integer $uniDataFormat
     * @return boolean
     */
    private function isValidUniqueDataFormat($uniDataFormat)
    {
        return in_array(
            $uniDataFormat,
            [
                self::NO_UNIQUE_PROCESS,
                self::SUITE_UNIQUE_VALUE,
                self::CEST_UNIQUE_VALUE,
                self::SUITE_UNIQUE_NOTATION,
                self::CEST_UNIQUE_NOTATION
            ],
            true
        );
    }
}
