<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

/**
 * Class BaseObjectExtractor
 */
class BaseObjectExtractor
{
    const NODE_NAME = 'nodeName';
    const NAME = 'name';
    const OBJ_DEPRECATED = 'deprecated';

    /**
     * BaseObjectExtractor constructor.
     */
    public function __construct()
    {
        // empty
    }

    /**
     * This method takes an array of data and an array representing irrelevant tags. The method strips
     * the data passed in of the irrelevant tags and returns the result.
     *
     * @param array $data
     * @param array ...$tags
     * @return array
     */
    protected function stripDescriptorTags($data, ...$tags)
    {
        $results = $data;
        foreach ($tags as $tag) {
            unset($results[$tag]);
        }

        return $results;
    }
}
