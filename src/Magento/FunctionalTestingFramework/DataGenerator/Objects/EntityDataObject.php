<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class EntityDataObject
 */
class EntityDataObject
{
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
     * @var array
     */
    private $linkedEntities = [];

    /**
     * An array of variable mappings for static data
     *
     * @var array
     */
    private $vars;

    /**
     * An array of Data Name to Data Value
     *
     * @var array
     */
    private $data = [];

    const NO_UNIQUE_PROCESS = 0;
    const SUITE_UNIQUE_VALUE = 1;
    const CEST_UNIQUE_VALUE = 2;
    const SUITE_UNIQUE_NOTATION = 3;
    const CEST_UNIQUE_NOTATION = 4;
    const SUITE_UNIQUE_FUNCTION = 'msqs';
    const CEST_UNIQUE_FUNCTION = 'msq';

    /**
     * Array of data name and its uniqueness attribute value.
     *
     * @var array
     */
    private $uniquenessData = [];

    /**
     * EntityDataObject constructor.
     * @param string $entityName
     * @param string $entityType
     * @param array $data
     * @param array $linkedEntities
     * @param array $uniquenessData
     * @param array $vars
     */
    public function __construct($entityName, $entityType, $data, $linkedEntities, $uniquenessData, $vars = [])
    {
        $this->name = $entityName;
        $this->type = $entityType;
        $this->data = $data;
        $this->linkedEntities = $linkedEntities;
        if ($uniquenessData) {
            $this->uniquenessData = $uniquenessData;
        }

        $this->vars = $vars;
    }

    /**
     * Getter for linked entity names
     *
     * @return array
     */
    public function getLinkedEntities()
    {
        return $this->linkedEntities;
    }

    /**
     * Getter for entity name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for entity type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for Entity's data.
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This function retrieves data from an entity defined in xml.
     *
     * @param string $dataName
     * @param int $uniDataFormat
     * @return string|null
     * @throws TestFrameworkException
     */
    public function getDataByName($dataName, $uniDataFormat)
    {
        if (!$this->isValidUniqueDataFormat($uniDataFormat)) {
            throw new TestFrameworkException(
                sprintf('Invalid unique data format value: %s \n', $uniDataFormat)
            );
        }

        $name = strtolower($dataName);

        if ($this->data !== null && array_key_exists($name, $this->data)) {
            $uniData = $this->getUniquenessDataByName($dataName);
            if (null === $uniData || $uniDataFormat == self::NO_UNIQUE_PROCESS) {
                return $this->data[$name];
            }

            switch ($uniDataFormat) {
                case self::SUITE_UNIQUE_VALUE:
                    if (!function_exists(self::SUITE_UNIQUE_FUNCTION)) {
                        throw new TestFrameworkException(
                            sprintf(
                                'Unique data format value: %s can only be used when running cests.\n',
                                $uniDataFormat
                            )
                        );
                    } elseif ($uniData == 'prefix') {
                        return msqs($this->getName()) . $this->data[$name];
                    } else { // $uniData == 'suffix'
                        return $this->data[$name] . msqs($this->getName());
                    }
                    break;
                case self::CEST_UNIQUE_VALUE:
                    if (!function_exists(self::CEST_UNIQUE_FUNCTION)) {
                        throw new TestFrameworkException(
                            sprintf(
                                'Unique data format value: %s can only be used when running cests.\n',
                                $uniDataFormat
                            )
                        );
                    } elseif ($uniData == 'prefix') {
                        return msq($this->getName()) . $this->data[$name];
                    } else { // $uniData == 'suffix'
                        return $this->data[$name] . msq($this->getName());
                    }
                    break;
                case self::SUITE_UNIQUE_NOTATION:
                    if ($uniData == 'prefix') {
                        return self::SUITE_UNIQUE_FUNCTION . '("' . $this->getName() . '")' . $this->data[$name];
                    } else { // $uniData == 'suffix'
                        return $this->data[$name] . self::SUITE_UNIQUE_FUNCTION . '("' . $this->getName() . '")';
                    }
                    break;
                case self::CEST_UNIQUE_NOTATION:
                    if ($uniData == 'prefix') {
                        return self::CEST_UNIQUE_FUNCTION . '("' . $this->getName() . '")' . $this->data[$name];
                    } else { // $uniData == 'suffix'
                        return $this->data[$name] . self::CEST_UNIQUE_FUNCTION . '("' . $this->getName() . '")';
                    }
                    break;
                default:
                    break;
            }
        }

        return null;
    }

    /**
     * Function which returns a reference to another entity (e.g. a var with entity="category" field="id" returns as
     * category->id)
     *
     * @param string $dataKey
     * @return array|null
     */
    public function getVarReference($dataKey)
    {
        if (array_key_exists($dataKey, $this->vars)) {
            return $this->vars[$dataKey];
        }

        return null;
    }

    /**
     * This function takes an array of entityTypes indexed by name and a string that represents the type of interest.
     * The function returns an array of entityNames relevant to the specified type.
     *
     * @param string $fieldType
     * @return array
     */
    public function getLinkedEntitiesOfType($fieldType)
    {
        $groupedArray = [];

        foreach ($this->linkedEntities as $entityName => $entityType) {
            if ($entityType == $fieldType) {
                $groupedArray[] = $entityName;
            }
        }

        return $groupedArray;
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
     * @param int $uniDataFormat
     * @return bool
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
