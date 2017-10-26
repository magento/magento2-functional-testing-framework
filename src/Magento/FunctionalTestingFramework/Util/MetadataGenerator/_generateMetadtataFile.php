<?php
require_once '../../../../../vendor/autoload.php';

const INPUT_TXT_FILE = 'input.yml';

// parse the input.yml file for context
$inputCfg = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(INPUT_TXT_FILE));

// create new MetadataGenUtil Object
$metadataGenUtil = new Magento\FunctionalTestingFramework\Util\MetadataGenerator\MetadataGenUtil(
    $inputCfg['operationName'],
    $inputCfg['operationDataType'],
    $inputCfg['operationUrl'],
    $inputCfg['inputString']
);

//generate the metadata file in the _output dir
$metadataGenUtil->generateMetadataFile();
