<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util\MetadataGenerator\Swagger;

use Doctrine\Common\Collections\ArrayCollection;
use Epfremme\Swagger\Entity\Schemas\SchemaInterface;
use Epfremme\Swagger\Entity\Schemas\ObjectSchema;
use Epfremme\Swagger\Entity\Schemas\RefSchema;
use Epfremme\Swagger\Entity\Schemas\ArraySchema;
use Epfremme\Swagger\Factory\SwaggerFactory;
use Epfremme\Swagger\Entity\Swagger;
use Epfremme\Swagger\Entity\Operation;
use Epfremme\Swagger\Entity\Parameters\BodyParameter;
use Epfremme\Swagger\Entity\Parameters\AbstractTypedParameter;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class MetadataGenerator
{
    const OUTPUT_DIR = '_operations';
    const OUTPUT_DIR2 = '_definitions';
    const INPUT_TXT_FILE = 'magento.json';
    const AUTH = 'adminOauth';
    const TEMPLATE_VAR_DEF_TYPE = 'create';

    const TEMPLATE_VAR_OP_NAME = 'operationName';
    const TEMPLATE_VAR_OP_DATATYPE = 'operationDataType';
    const TEMPLATE_VAR_OP_TYPE = 'operationType';
    const TEMPLATE_VAR_OP_AUTH = 'auth';
    const TEMPLATE_VAR_OP_URL = 'operationUrl';
    const TEMPLATE_VAR_OP_METHOD = 'method';

    const TEMPLATE_VAR_OP_FIELD = 'fields';
    const TEMPLATE_VAR_FIELD_NAME = 'fieldName';
    const TEMPLATE_VAR_FIELD_TYPE = 'fieldType';
    const TEMPLATE_VAR_FIELD_IS_REQUIRED = 'isRequired';

    const TEMPLATE_VAR_OP_PARAM = 'params';
    const TEMPLATE_VAR_PARAM_NAME = 'paramName';
    const TEMPLATE_VAR_PARAM_TYPE = 'paramType';

    const TEMPLATE_VAR_OP_ARRAY = 'arrays';
    const TEMPLATE_VAR_ARRAY_KEY = 'arrayKey';
    const TEMPLATE_VAR_ARRAY_IS_REQUIRED = 'isRequiredArray';
    const TEMPLATE_VAR_VALUES = 'values';
    const TEMPLATE_VAR_VALUE = 'value';

    const REF_REGEX = "~#/definitions/([\S]+)~";

    /**
     * Mustache Engine instance for the templating.
     *
     * @var Mustache_Engine
     */
    private $mustache_engine;

    /**
     * Swagger built from json.
     *
     * @var Swagger
     */
    private static $swagger;

    /**
     * Path params.
     *
     * @var string
     */
    private $pathParams;

    /**
     * Array to hold operation query params.
     *
     * @var array
     */
    private $params;

    /**
     * Array to hold operation fields.
     *
     * @var array
     */
    private $fields;

    /**
     * The relative filepath for the *meta.xml file to be generated.
     *
     * @var string
     */
    private $filepath;

    /**
     * Operation method mapping.
     *
     * @var array
     */
    private static $methodMapping = [
        'POST' => 'create',
        'DELETE' => 'delete',
        'PUT' => 'update',
        'GET' => 'get',
    ];

    /**
     * Build and initialize generator.
     */
    public function __construct()
    {
        self::buildSwaggerSpec();
        $this->initMustacheTemplates();
    }

    /**
     * Parse swagger spec from input json file.
     * TODO: read swagger spec from magento server.
     *
     * @return void
     */
    public function generateMetadataFromSwagger()
    {
        $paths = self::$swagger->getPaths();

        foreach ($paths->getIterator() as $pathKey => $path) {
            $operations = $path->getOperations();
            foreach ($operations->getIterator() as $operationKey => $operation) {
                $this->renderOperation($operation, $pathKey, $operationKey);
            }
        }

        $definitions = self::$swagger->getDefinitions();
        foreach ($definitions->getIterator() as $defKey => $definition) {
            $this->renderDefinition($defKey, $definition);
        }
    }

    /**
     * Render swagger operations.
     *
     * @param Operation $operation
     * @param string    $path
     * @param string    $method
     * @return void
     */
    private function renderOperation($operation, $path, $method)
    {
        $operationArray = [];
        $this->pathParams = '';
        $this->params = [];
        $this->fields = [];
        $operationMethod = strtoupper($method);
        $operationDataType = ucfirst($operation->getOperationId());

        $operationArray[self::TEMPLATE_VAR_OP_NAME] = self::$methodMapping[$operationMethod] . $operationDataType;
        $operationArray[self::TEMPLATE_VAR_OP_DATATYPE] = $operationDataType;
        $operationArray[self::TEMPLATE_VAR_OP_METHOD] = $operationMethod;
        $operationArray[self::TEMPLATE_VAR_OP_AUTH] = self::AUTH;
        $operationArray[self::TEMPLATE_VAR_OP_TYPE] = self::$methodMapping[$operationMethod];
        $operationArray[self::TEMPLATE_VAR_OP_URL] = $path;

        $params = $operation->getParameters();
        if (!empty($params)) {
            $this->parseParams($params, $path);
            $operationArray[self::TEMPLATE_VAR_OP_FIELD] = $this->fields;
            $operationArray[self::TEMPLATE_VAR_OP_PARAM] = $this->params;
        }

        if (!empty($this->pathParams)) {
            $operationArray[self::TEMPLATE_VAR_OP_URL] .= $this->pathParams;
        }

        $this->generateMetaDataFile(
            self::OUTPUT_DIR,
            $operationDataType,
            'operation',
            $operationArray
        );
    }

    /**
     * Render swagger definitions.
     *
     * @param string                   $defKey
     * @param ObjectSchema|ArraySchema $definition
     * @return void
     */
    private function renderDefinition($defKey, $definition)
    {
        $operationArray = [];
        $this->fields = [];

        $operationArray[self::TEMPLATE_VAR_OP_NAME] = $defKey;
        $operationArray[self::TEMPLATE_VAR_OP_DATATYPE] = $defKey;
        $operationArray[self::TEMPLATE_VAR_OP_TYPE] = self::TEMPLATE_VAR_DEF_TYPE;

        if ($definition instanceof ObjectSchema) {
            $properties = $definition->getProperties();
            if (!empty($properties)) {
                $dataField = [];
                $dataArray = [];
                foreach ($properties->getIterator() as $propertyKey => $property) {
                    if ($property instanceof ArraySchema) {
                        $dataArray[] = $this->parseSchema($property, $propertyKey, 1, 1);
                    } else {
                        $dataField[] = $this->parseSchema($property, $propertyKey, 0, 1);
                    }
                }
                if (!empty($dataField)) {
                    $operationArray[self::TEMPLATE_VAR_OP_FIELD] = $dataField;
                }
                if (!empty($dataArray)) {
                    foreach ($dataArray as $array) {
                        $operationArray[self::TEMPLATE_VAR_OP_ARRAY.'1'][] = $array[self::TEMPLATE_VAR_OP_ARRAY.'1'];
                    }
                }
            }
        } elseif ($definition instanceof ArraySchema) {
            $operationArray = array_merge($operationArray, $this->parseSchema($definition, $defKey, 1, 1));
        }

        $this->generateMetaDataFile(
            self::OUTPUT_DIR2,
            $defKey,
            'definition',
            $operationArray
        );
    }

    /**
     * Parse schema and return an array that will be consumed by mustache template engine.
     *
     * @param SchemaInterface $schema
     * @param string          $name
     * @param boolean         $forArray
     * @param integer         $depth
     * @return array
     */
    private function parseSchema($schema, $name, $forArray, $depth)
    {
        $data = [];

        if ($schema instanceof RefSchema) {
            $ref = $schema->getRef();
            preg_match(self::REF_REGEX, $ref, $matches);
            if (count($matches) == 2) {
                if (!$forArray) {
                    $data[self::TEMPLATE_VAR_FIELD_NAME] = $name;
                    $data[self::TEMPLATE_VAR_FIELD_TYPE] = $matches[1];
                } else {
                    $data[self::TEMPLATE_VAR_VALUES][] = [self::TEMPLATE_VAR_VALUE => $matches[1]];
                }
            }
        } elseif ($schema instanceof ArraySchema) {
            $values = [];
            $items = $schema->getItems();
            $data[self::TEMPLATE_VAR_OP_ARRAY.$depth][self::TEMPLATE_VAR_ARRAY_KEY] = $name;
            if ($items instanceof ArrayCollection) {
                foreach ($items->getIterator() as $itemKey => $item) {
                    $values[] = $this->parseSchema($item, $itemKey, 1, $depth+1);
                }
                $data[self::TEMPLATE_VAR_VALUES] = $values;
                $data[self::TEMPLATE_VAR_OP_ARRAY.$depth] = $data;
            } else {
                $data[self::TEMPLATE_VAR_OP_ARRAY.$depth] = array_merge(
                    $data[self::TEMPLATE_VAR_OP_ARRAY.$depth],
                    $this->parseSchema($items, $name, 1, $depth+1)
                );
            }
        } else {
            if (method_exists($schema, 'getType')) {
                if (!$forArray) {
                    $data[self::TEMPLATE_VAR_FIELD_NAME] = $name;
                    $data[self::TEMPLATE_VAR_FIELD_TYPE] = $schema->getType();
                } else {
                    $data[self::TEMPLATE_VAR_VALUES][] = [self::TEMPLATE_VAR_VALUE => $schema->getType()];
                }
            }
        }
        return $data;
    }

    /**
     * Parse params for an operation.
     *
     * @param ArrayCollection $params
     * @param string          $path
     * @return void
     */
    private function parseParams($params, $path)
    {
        foreach ($params->getIterator() as $paramKey => $param) {
            if (empty($param)) {
                continue;
            }

            $paramIn = $param->getIn();
            if ($paramIn == 'body') {
                $this->setBodyParams($param);
            } elseif ($paramIn == 'path') {
                $this->setPathParams($param, $path);
            } elseif ($paramIn == 'query') {
                $this->setQueryParams($param);
            }
        }
    }

    /**
     * Set body params for an operation.
     *
     * @param BodyParameter $param
     * @return void
     */
    private function setBodyParams($param)
    {
        $this->fields = [];
        $required = [];

        $paramSchema = $param->getSchema();
        $paramSchemaRequired = $paramSchema->getRequired();
        if (!empty($paramSchemaRequired)) {
            foreach ($paramSchemaRequired as $i => $key) {
                $required[] = $key;
            }
        }
        $paramSchemaProperties = $paramSchema->getProperties();
        foreach ($paramSchemaProperties->getIterator() as $paramPropertyKey => $paramSchemaProperty) {
            $field = [];
            $field[self::TEMPLATE_VAR_FIELD_NAME] = $paramPropertyKey;
            $field[self::TEMPLATE_VAR_FIELD_TYPE] = $paramSchemaProperty->getType();
            if ($field[self::TEMPLATE_VAR_FIELD_TYPE] == 'ref') {
                preg_match(self::REF_REGEX, $paramSchemaProperty->getRef(), $matches);
                if (count($matches) == 2) {
                    $field[self::TEMPLATE_VAR_FIELD_TYPE] = $matches[1];
                }
            }
            if (in_array($paramPropertyKey, $required)) {
                $field[self::TEMPLATE_VAR_FIELD_IS_REQUIRED] = 'true';
            } else {
                $field[self::TEMPLATE_VAR_FIELD_IS_REQUIRED] = 'false';
            }
            $this->fields[] = $field;
        }
    }

    /**
     * Set path params for an operation.
     *
     * @param AbstractTypedParameter $param
     * @param string                 $path
     * @return void
     */
    private function setPathParams($param, $path)
    {
        $pathParamStr = '{' . $param->getName() . '}';
        if (strpos($path, $pathParamStr) === false) {
            $this->pathParams .= '/' . $pathParamStr;
        }
    }

    /**
     * Set query params for an operation.
     *
     * @param AbstractTypedParameter $param
     * @return void
     */
    private function setQueryParams($param)
    {
        $query = [];
        $query[self::TEMPLATE_VAR_PARAM_NAME] = $param->getName();
        $query[self::TEMPLATE_VAR_PARAM_TYPE] = $param->getType();

        $this->params[] = $query;
    }

    /**
     * Build swagger spec from factory.
     *
     * @return void
     */
    private static function buildSwaggerSpec()
    {
        $factory = new SwaggerFactory();
        self::$swagger = $factory->build(self::INPUT_TXT_FILE);
    }

    /**
     * Function which initializes mustache templates for file generation.
     *
     * @return void
     */
    private function initMustacheTemplates()
    {
        $this->mustache_engine = new Mustache_Engine(
            ['loader' => new Mustache_Loader_FilesystemLoader("views"),
                'partials_loader' => new Mustache_Loader_FilesystemLoader(
                    "views" . DIRECTORY_SEPARATOR . "partials"
                )]
        );
    }

    /**
     * Render template and generate a metadata file.
     *
     * @param string $relativeDir
     * @param string $fileName
     * @param string $template
     * @param array  $data
     * @return void
     */
    private function generateMetaDataFile($relativeDir, $fileName, $template, $data)
    {
        $this->filepath = $relativeDir . DIRECTORY_SEPARATOR . $fileName . "-meta.xml";
        $result = $this->mustache_engine->render($template, $data);
        $this->cleanAndCreateOutputDir();
        file_put_contents(
            $this->filepath,
            $result
        );
    }

    /**
     * Function which cleans any previously created fileand creates the _output dir.
     *
     * @return void
     */
    private function cleanAndCreateOutputDir()
    {
        if (!file_exists(self::OUTPUT_DIR)) {
            mkdir(self::OUTPUT_DIR);
        }

        if (!file_exists(self::OUTPUT_DIR2)) {
            mkdir(self::OUTPUT_DIR2);
        }

        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
    }
    /*
    private static function debugData() {
        $paramsExample = ['params' =>
            [
                'paramName' => 'name',
                'paramType' => 'type'
            ],
            [
                'paramName' => 'name',
                'paramType' => 'type'
            ],
            [
                'paramName' => 'name',
                'paramType' => 'type'
            ],
        ];
        $fieldsExample = ['fields' =>
            [
                'fieldName' => 'name',
                'fieldType' => 'type',
                'isRequired' => true,
            ],
            [
                'fieldName' => 'name',
                'fieldType' => 'type',
                'isRequired' => true,
            ],
            [
                'fieldName' => 'name',
                'fieldType' => 'type',
                'isRequired' => true,
            ],
        ];
        $arraysExample = ['arrays1' =>
            [
                'arrayKey' => 'someKey',
                'values' => [
                    'type1',
                    'type2',
                ],
                'arrays2' => [
                    'arrayKey' => 'otherKey',
                    'values' => [
                        'type3',
                        'type4',
                    ],
                    'arrays3' => [
                        'arrayKey' => 'anotherKey',
                        'values' => [
                            'type5',
                            'type6',
                        ],
                    ],
                ],
            ],
            [
                'arrayKey' => 'someKey',
                'values' => [
                    [
                        'value' => 'type1',
                    ],
                    [
                        'value' => 'type2',
                    ],
                ],
                'arrays2' => [
                    'arrayKey' => 'otherKey',
                    'values' => [
                        [
                            'value' => 'type3',
                        ],
                        [
                            'value' => 'type4',
                        ],
                    ],
                    'arrays3' => [
                        'arrayKey' => 'anotherKey',
                        'values' => [
                            [
                                'value' => 'type5',
                            ],
                            [
                                'value' => 'type6',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    */
}
