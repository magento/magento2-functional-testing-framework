<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;


class FieldGroupXmlObject
{
    private $name;
    private $assertions = array(); // Array of assertions
    private $dataNames = array(); // Array of data names

    public function __construct($dataObjectName, $assertions, $data)
    {
        $this->name = $dataObjectName;
        $this->assertions = $assertions;
        $this->dataNames = $data;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAssertions()
    {
        return $this->assertions;
    }

    public function getDataNames()
    {
        return $this->dataNames;
    }
}
