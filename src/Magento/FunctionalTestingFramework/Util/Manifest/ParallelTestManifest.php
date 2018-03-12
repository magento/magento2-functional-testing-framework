<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Manifest;

use Magento\Framework\Exception\RuntimeException;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Util\Filesystem\DirSetupUtil;

class ParallelTestManifest extends BaseTestManifest
{
    const PARALLEL_CONFIG = 'parallel';

    /**
     * An associate array of test name to size of test.
     *
     * @var string[]
     */
    private $testNameToSize = [];

    /**
     * Path to the directory that will contain all test group files
     *
     * @var string
     */
    private $dirPath;

    /**
     * TestManifest constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->dirPath = $path . DIRECTORY_SEPARATOR . 'groups';
        parent::__construct($path, self::PARALLEL_CONFIG);
    }

    /**
     * Takes a test name and set of tests, records the names in a file for codeception to consume.
     *
     * @param TestObject $testObject
     * @return void
     */
    public function addTest($testObject)
    {
        $this->testNameToSize[$testObject->getCodeceptionName()] = $testObject->getTestActionCount();
    }

    /**
     * Function which generates the actual manifest once the relevant tests have been added to the array.
     *
     * @param int $nodes
     * @return void
     */
    public function generate($nodes = null)
    {
        if ($nodes == null) {
            $nodes = 2;
        }

        DirSetupUtil::createGroupDir($this->dirPath);
        arsort($this->testNameToSize);
        $node = $nodes;

        foreach (array_keys($this->testNameToSize) as $testName) {
            $node = $this->getNodeOrder($node, $nodes);
            $nodeString = strval($node);
            $fileResource = fopen($this->dirPath . DIRECTORY_SEPARATOR .  "group{$nodeString}.txt", 'a');
            $line = $this->relativeDirPath . DIRECTORY_SEPARATOR . $testName . '.php';
            fwrite($fileResource, $line . PHP_EOL);
            fclose($fileResource);
        }
    }

    /**
     * Function which independently iterates node position based on number of desired nodes.
     *
     * @param int $currentNode
     * @param int $nodes
     * @return int
     */
    private function getNodeOrder($currentNode, $nodes)
    {
        $adjustedRef = $currentNode + 1;
        if ($adjustedRef <= $nodes) {
            return $currentNode + 1;
        }

        return 1;
    }
}
