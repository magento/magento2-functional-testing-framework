<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Data\Argument\Interpreter;

use Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;
use Magento\FunctionalTestingFramework\Stdlib\BooleanUtils;

/**
 * Interpreter of boolean data type, such as boolean itself or boolean string
 */
class Boolean implements InterpreterInterface
{
    /**
     * Utility methods for the boolean data type
     *
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * Boolean constructor.
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     * @return boolean
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
