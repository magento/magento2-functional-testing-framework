<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;

class EntityDataObjectBuilder
{
    /**
     * Array of data fields for the object. Has fields contained by default.
     *
     * @var array
     */
    private $data = [
        "name" => "Hopper",
        "gpa" => "3.5678",
        "phone" => "5555555",
        "isprimary" => "true"
    ];

    /**
     * Name of the data object.
     *
     * @var string
     */
    private $name = "testDataObject";

    /**
     * Name of the data object type (e.g. customer, category etc.)
     *
     * @var string
     */
    private $type = "testType";

    /**
     * A flat array containing linked entity name => linked entity type.
     *
     * @var array
     */
    private $linkedEntities = [];

    /**
     * An array contain references to data to be resolved by the api.
     *
     * @var array
     */
    private $vars = [];

    /**
     * A function which will build an Entity Data Object with the params specified by the object.
     *
     * @return EntityDataObject
     */
    public function build()
    {
        return new EntityDataObject(
            $this->name,
            $this->type,
            $this->data,
            $this->linkedEntities,
            null,
            $this->vars
        );
    }

    /**
     * Sets the name of the EntityDataObject.
     *
     * @param string $name
     * @return EntityDataObjectBuilder
     */
    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the type of the EntityDataObject.
     *
     * @param string $type
     * @return EntityDataObjectBuilder
     */
    public function withType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets the data fields on the object to the data field array specified in the argument.
     *
     * @param array $fields
     * @return EntityDataObjectBuilder
     */
    public function withDataFields($fields)
    {
        $this->data = $fields;
        return $this;
    }

    /**
     * Sets the linked entities specified by the user as a param for Entity Data Object creation.
     *
     * @param array $linkedEntities
     * @return EntityDataObjectBuilder
     */
    public function withLinkedEntities($linkedEntities)
    {
        $this->linkedEntities = $linkedEntities;
        return $this;
    }
}
