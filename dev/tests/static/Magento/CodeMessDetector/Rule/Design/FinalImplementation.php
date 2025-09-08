<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\CodeMessDetector\Rule\Design;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\MethodAware;

/**
 * Magento is a highly extensible and customizable platform.
 * Usage of final classes and methods is prohibited.
 */
class FinalImplementation extends AbstractRule implements ClassAware, MethodAware
{
    /**
     * @inheritdoc
     */
    public function apply(AbstractNode $node)
    {
        if ($node->isFinal()) {
            $this->addViolation($node, [$node->getType(), $node->getFullQualifiedName()]);
        }
    }
}
