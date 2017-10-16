<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\verification\Util;

class FileDiffUtil
{
    /**
     * Object which represents the expected file
     *
     * @var array|bool
     */
    private $expectedFile;


    /**
     * Object which represents the actual file
     *
     * @var array|bool
     */
    private $actualFile;

    /**
     * FileDiffUtil constructor.
     *
     * @param string $expectedFilePath
     * @param string $actualFilePath
     */
    public function __construct($expectedFilePath, $actualFilePath)
    {
        $this->expectedFile = file($expectedFilePath);
        $this->actualFile = file($actualFilePath);
    }

    /**
     * Function which does a line by line comparison between the contents of the two files fed to the constructor.
     *
     * @return null|string
     */
    public function diffContents()
    {
        $differingContent = null;
        foreach ($this->actualFile as $line_num => $line) {
            if ($line != $this->expectedFile[$line_num]) {
                return $this->expectedFile[$line_num] . "was expected, but found: ${line} on line ${line_num}.";
            }
        }

        return $differingContent;
    }
}