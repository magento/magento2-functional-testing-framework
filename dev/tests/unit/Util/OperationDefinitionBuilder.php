<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;

class OperationDefinitionBuilder
{
    /**
     * Name of the operation definition
     *
     * @var string
     */
    private $name;

    /**
     * Name of the operation for the operation definition
     *
     * @var string
     */
    private $operation;

    /**
     * Type of the operation for the operation defintions (e.g. create, delete)
     *
     * @var string
     */
    private $type;

    /**
     * An array containing the metadata definition for an object to be mapped against the API.
     *
     * @var array
     */
    private $metadata = [];

    /**
     * Determines if api URL should remove magento_backend_name.
     * @var boolean
     */
    private $removeBackend;

    /**
     * Function which builds an operation defintions based on the fields set by the user.
     *
     * @return OperationDefinitionObject
     */
    public function build()
    {
        return new OperationDefinitionObject(
            $this->name,
            $this->operation,
            $this->type,
            null,
            null,
            null,
            null,
            null,
            $this->metadata,
            null,
            false
        );
    }

    /**
     * Sets the name of the operation definition to be built.
     *
     * @param string $name
     * @return OperationDefinitionBuilder
     */
    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the name of the operation for the object to be built.
     *
     * @param string $operation
     * @return OperationDefinitionBuilder
     */
    public function withOperation($operation)
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * Sets the name of the type of operation (e.g. create, delete)
     *
     * @param string $type
     * @return OperationDefinitionBuilder
     */
    public function withType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Takes an array of values => type or an array of operation elements and transforms into operation metadata.
     *
     * @param array $metadata
     * @return OperationDefinitionBuilder
     */
    public function withMetadata($metadata)
    {
        $primitives = [];
        foreach ($metadata as $fieldName => $value) {
            // type check here TODO
            if (is_string($value)) {
                $primitives[$fieldName] = $value;
            } else {
                $this->metadata[] = $value;
            }
        }

        $this->metadata = array_merge(
            $this->metadata,
            OperationElementBuilder::buildOperationElementFields($primitives)
        );
        return $this;
    }
}
