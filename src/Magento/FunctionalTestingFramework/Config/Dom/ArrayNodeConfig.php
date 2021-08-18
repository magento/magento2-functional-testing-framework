<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Config\Dom;

/**
 * Configuration of nodes that represent numeric or associative arrays
 */
class ArrayNodeConfig
{
    /**
     * Matching of XPath expressions to path patterns.
     *
     * @var NodePathMatcher
     */
    private $nodePathMatcher;

    /**
     * Format: array('/associative/array/path' => '<array_key_attribute>', ...)
     *
     * @var array
     */
    private $assocArrays = [];

    /**
     * Flat array of expanded patterns for matching xpath
     *
     * @var array
     */
    private $flatAssocArray = [];

    /**
     * Format: array('/numeric/array/path', ...)
     *
     * @var array
     */
    private $numericArrays = [];

    /**
     * ArrayNodeConfig constructor.
     * @param NodePathMatcher $nodePathMatcher
     * @param array           $assocArrayAttributes
     * @param array           $numericArrays
     */
    public function __construct(
        NodePathMatcher $nodePathMatcher,
        array $assocArrayAttributes,
        array $numericArrays = []
    ) {
        $this->nodePathMatcher = $nodePathMatcher;
        $this->assocArrays = $assocArrayAttributes;
        $this->flatAssocArray = $this->flattenToAssocKeyAttributes($assocArrayAttributes);
        $this->numericArrays = $numericArrays;
    }

    /**
     * Whether a node is a numeric array or not
     *
     * @param string $nodeXpath
     * @return boolean
     */
    public function isNumericArray($nodeXpath)
    {
        foreach ($this->numericArrays as $pathPattern) {
            if ($this->nodePathMatcher->pathMatch($pathPattern, $nodeXpath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve name of array key attribute, if a node is an associative array
     *
     * @param string $nodeXpath
     * @return string|null
     */
    public function getAssocArrayKeyAttribute($nodeXpath)
    {
        if (array_key_exists($nodeXpath, $this->flatAssocArray)) {
            return $this->flatAssocArray[$nodeXpath];
        }

        foreach ($this->assocArrays as $pathPattern => $keyAttribute) {
            if ($this->nodePathMatcher->pathMatch($pathPattern, $nodeXpath)) {
                return $keyAttribute;
            }
        }
        return null;
    }

    /**
     * Function which takes a patterned list of xpath matchers and flattens to a single level array for
     * performance improvement
     *
     * @param array $assocArrayAttributes
     * @return array
     */
    private function flattenToAssocKeyAttributes($assocArrayAttributes)
    {
        $finalPatterns = [];
        foreach ($assocArrayAttributes as $pattern => $key) {
            $vars = explode("/", ltrim($pattern, "/"));
            $stringPatterns = [""];
            foreach ($vars as $var) {
                if (strstr($var, "|")) {
                    $repOpen = str_replace("(", "", $var);
                    $repClosed = str_replace(")", "", $repOpen);
                    $nestedPatterns = explode("|", $repClosed);
                    $stringPatterns = $this->mergeStrings($stringPatterns, $nestedPatterns);
                    continue;
                }

                // append this path to all of the paths that currently exist
                array_walk($stringPatterns, function (&$value, $key) use ($var) {
                    $value .= "/" . $var;
                });
            }

            $finalPatterns = array_merge($finalPatterns, array_fill_keys($stringPatterns, $key));
        }

        return $finalPatterns;
    }

    /**
     * Takes 2 arrays and appends all string in the second array to each entry in the first.
     *
     * @param string[] $parentStrings
     * @param string[] $childStrings
     * @return array
     */
    private function mergeStrings($parentStrings, $childStrings)
    {
        $result = [];
        foreach ($parentStrings as $pString) {
            foreach ($childStrings as $cString) {
                $result[] = $pString . "/" . $cString;
            }
        }

        return $result;
    }
}
