<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Data\Argument\Interpreter;

use Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface;
use Magento\FunctionalTestingFramework\Stdlib\BooleanUtils;

class DataObject implements InterpreterInterface
{
    /**
     * Utility methods for the boolean data type.
     *
     * @var \Magento\FunctionalTestingFramework\Stdlib\BooleanUtils
     */
    protected $booleanUtils;

    /**
     * DataObject constructor.
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
     * @return array
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
