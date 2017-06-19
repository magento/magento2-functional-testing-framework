<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

use Magento\AcceptanceTestFramework\DataGenerator\DataGeneratorXMLConstants;

class EntityXmlObject
{
    private $name;
    private $type;
    private $dataConfigs = array();
    private $fieldGroups = array(); //array of data objects map of DataObject Name to actual Data Name
    private $data = array(); //array of Data Name to Data Value


    public function __construct($entityName, $entityType, $dataConfigs, $dataObjects)
    {
        $this->name = $entityName;
        $this->type = $entityType;

        foreach ($dataConfigs as $dataConfig) {
            $this->dataConfigs[] = $dataConfig[DataGeneratorXMLConstants::DATA_CONFIG_VALUE];
        }

        foreach ($dataObjects as $fieldGroupName => $fieldGroupObject) {
            $dataNames = array(); // array to store names of data per fieldGroupObject
            $assertions = array(); // array to store assertions

            foreach ($fieldGroupObject[DataGeneratorXMLConstants::DATA_OBJECT_DATA] as $dataElement) {
                $dataNames[] = $dataElement[DataGeneratorXMLConstants::DATA_ELEMENT_KEY];
                $this->data[$dataElement[DataGeneratorXMLConstants::DATA_ELEMENT_KEY]] =
                    $dataElement[DataGeneratorXMLConstants::DATA_ELEMENT_VALUE];
            }

            foreach ($fieldGroupObject[DataGeneratorXMLConstants::DATA_OBJECT_ASSERTS] as $assertion) {
                $assertions[] = $assertion[DataGeneratorXMLConstants::ASSERT_VALUE];
            }


            $fieldGroupXmlObject = new FieldGroupXmlObject($fieldGroupName, $assertions, $dataNames);
            $this->fieldGroups[$fieldGroupXmlObject->getName()] = $fieldGroupXmlObject;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFieldGroups()
    {
        return $this->fieldGroups;
    }

    public function getData()
    {
        return $this->data;
    }
}
