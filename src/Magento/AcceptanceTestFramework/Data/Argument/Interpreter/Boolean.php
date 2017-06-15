<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Data\Argument\Interpreter;

use Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface;
use Magento\AcceptanceTestFramework\Stdlib\BooleanUtils;

/**
 * Interpreter of boolean data type, such as boolean itself or boolean string
 */
class Boolean implements InterpreterInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function evaluate(array $data)
    {
        if (!isset($data['value'])) {
            throw new \InvalidArgumentException('Boolean value is missing.');
        }
        $value = $data['value'];
        return $this->booleanUtils->toBoolean($value);
    }
}
