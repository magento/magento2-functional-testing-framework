<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\MetadataGenerator\FormData;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class MetadataGenUtil
{
    const OUTPUT_DIR = '_output';
    const INPUT_TXT_FILE = 'input.yml';

    /**
     * Mustache Engine instance for the templating.
     *
     * @var Mustache_Engine
     */
    private $mustacheEngine;

    /**
     * Name of the operation (e.g. createCategory)
     *
     * @var string
     */
    private $operationName;

    /**
     * Data type of the operation (e.g. category)
     *
     * @var string
     */
    private $operationDataType;

    /**
     * Url path for the operation (e.g. /admin/system_config/save/section/payment)
     *
     * @var string
     */
    private $operationUrl;

    /**
     * The raw parameter data to be converted into metadata
     * (e.g. entity[param1]=value1&entity[param2]=value2&entity[param3]=value3&entityField=field1)
     *
     * @var string
     */
    private $inputString;

    /**
     * The relative filepath for the *meta.xml file to be generated.
     *
     * @var string
     */
    private $filepath;

    /**
     * MetadataGenUtil constructor.
     *
     * @param string $operationName
     * @param string $operationDataType
     * @param string $operationUrl
     * @param string $inputString
     */
    public function __construct($operationName, $operationDataType, $operationUrl, $inputString)
    {
        $this->operationName = $operationName;
        $this->operationDataType = $operationDataType;
        $this->operationUrl = $operationUrl;
        $this->inputString = $inputString;

        $this->filepath = self::OUTPUT_DIR . DIRECTORY_SEPARATOR . $this->operationDataType . "-meta.xml";
    }

    /**
     * Function which takes params from constructor, transforms into data array and outputs a representative metadata
     * file for MFTF to consume and send requests.
     *
     * @return void
     */
    public function generateMetadataFile()
    {
        // Load Mustache templates
        $this->mustacheEngine = new Mustache_Engine(
            ['loader' => new Mustache_Loader_FilesystemLoader("views"),
                'partials_loader' => new Mustache_Loader_FilesystemLoader(
                    "views" . DIRECTORY_SEPARATOR . "partials"
                )]
        );

        // parse the string params into an array
        parse_str($this->inputString, $results);
        $data = $this->convertResultToEntry($results, $this->operationDataType);
        $data = $this->appendParentParams($data);
        $output = $this->mustacheEngine->render('operation', $data);
        $this->cleanAndCreateOutputDir();
        file_put_contents(
            $this->filepath,
            $output
        );
    }

    /**
     * Function which takes the top level params from the user and returns an array appended with the needed config.
     *
     * @param array $data
     * @return array
     */
    private function appendParentParams($data)
    {
        $result = $data;
        $result['operationName'] = $this->operationName;
        $result['operationDataType'] = $this->operationDataType;
        $result['operationUrl'] = $this->operationUrl;

        return $result;
    }

    /**
     * Function which is called recursively to generate the mustache array for the template enging. Makes decisions
     * about type and format based on parameter array.
     *
     * @param array  $results
     * @param string $defaultDataType
     * @return array
     */
    private function convertResultToEntry($results, $defaultDataType)
    {
        $data = [];

        foreach ($results as $key => $result) {
            $entry = [];
            if (is_array($result)) {
                $entry = array_merge($entry, ['objectName' => $key]);
                $res = $this->convertResultToEntry($result, $defaultDataType);
                if (!array_key_exists('objects', $res)) {
                    $entry = array_merge($entry, ['objects' => null]);
                    $entry = array_merge($entry, ['dataType' => $key]);
                } else {
                    $entry = array_merge($entry, ['hasChildObj' => true]);
                    $entry = array_merge($entry, ['dataType' => $defaultDataType]);
                }
                $data['objects'][] = array_merge($entry, $res);
            } else {
                $data['fields'][] = ['fieldName' => $key];
            }
        }

        return $data;
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

        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
    }
}
