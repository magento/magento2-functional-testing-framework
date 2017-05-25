<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Data\Argument\Interpreter;

use Magento\TestFramework\Data\Argument\InterpreterInterface;

/**
 * Interpreter of string data type that may optionally perform text translation
 */
class StringUtils implements InterpreterInterface
{
    /**
     * {@inheritdoc}
     * @return string
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (isset($data['value'])) {
            $result = $data['value'];
            if (!is_string($result)) {
                throw new \InvalidArgumentException('String value is expected.');
            }
        } else {
            $result = '';
        }
        return $result;
    }
}
