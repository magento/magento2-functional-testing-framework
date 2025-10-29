<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Config\Dom;

/**
 * Matching of XPath expressions to path patterns
 */
class NodePathMatcher
{
    /**
     * Whether a subject XPath matches to a given path pattern
     *
     * @param string $pathPattern  Example: '/some/static/path' or '/some/regexp/path(/item)+'.
     * @param string $xpathSubject Example: '/some[@attr="value"]/static/ns:path'.
     * @return boolean
     */
    public function pathMatch($pathPattern, $xpathSubject)
    {
        $pathSubject = $this->simplifyXpath($xpathSubject);
        $pathPattern = '#^' . $pathPattern . '$#';
        return (bool)preg_match($pathPattern, $pathSubject);
    }

    /**
     * Strip off predicates and namespaces from the XPath
     *
     * @param string $xpath
     * @return string
     */
    public function simplifyXpath($xpath)
    {
        $result = $xpath;
        $result = preg_replace('/\[@[^\]]+?\]/', '', $result);
        $result = preg_replace('/\/[^:]+?\:/', '/', $result);
        return $result;
    }
}
