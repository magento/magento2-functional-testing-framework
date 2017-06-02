<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Data\Argument\Interpreter;

use Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface;
use Magento\AcceptanceTestFramework\Stdlib\BooleanUtils;

class DataObject implements InterpreterInterface
{
    /**
     * @var \Magento\AcceptanceTestFramework\Stdlib\BooleanUtils
     */
    protected $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * Compute and return effective value of an argument
     *
     * @param array $data
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function evaluate(array $data)
    {
        $result = ['instance' => $data['value']];
        if (isset($data['shared'])) {
            $result['shared'] = $this->booleanUtils->toBoolean($data['shared']);
        }
        return $result;
    }
}
