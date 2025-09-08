<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Data\Argument\Interpreter;

use Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;

/**
 * Interpreter of numeric data, such as integer, float, or numeric string
 */
class Number implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     * @return string|integer|float
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['value']) || !is_numeric($data['value'])) {
            throw new \InvalidArgumentException('Numeric value is expected.');
        }
        $result = $data['value'];
        return $result;
    }
}
