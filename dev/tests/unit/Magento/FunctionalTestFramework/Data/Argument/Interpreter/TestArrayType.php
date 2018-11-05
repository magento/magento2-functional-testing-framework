<?php

use Magento\FunctionalTestingFramework\Data\Argument\Interpreter\ArrayType;
use PHPUnit\Framework\TestCase;

/**
 * Class TestArrayType
 * @covers \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\ArrayType
 */
class TestArrayType extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
     */
    private $itemInterpreterMock;

    /**
     * @var \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\ArrayType
     */
    private $arrayType;

    public function SetUp()
    {
        $this->itemInterpreterMock = $this->getMockForAbstractClass(\Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface::class);
        $this->arrayType = new ArrayType($this->itemInterpreterMock);
    }

    public function testEvaluateReturnsNoItemsWhenDataContainsNoItemKey()
    {
        $dataStub = [];

        $evaluateReturn = $this->arrayType->evaluate($dataStub);

        $this->assertInternalType('array', $evaluateReturn);
        $this->assertEmpty($evaluateReturn);
    }

    public function testEvaluateThrowsAnExceptionWhenItemHasInvalidType()
    {
        $dataStub = [
            'item' => 'NotAnArray'
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Array items are expected');

        $this->arrayType->evaluate($dataStub);
    }

    public function testEvaluateReturnsItemsInAscendingOrderAccordingToSortOrderValue()
    {
        $dataStub = [
            'item' => [
                10 => [
                    'sortOrder' => 20
                ],
                20 => [
                    'sortOrder' => 10
                ]
            ]
        ];

        // Evaluate results does not really matter now
        $this->itemInterpreterMock->method('evaluate')
            ->willReturn([]);

        $evaluateReturn = $this->arrayType->evaluate($dataStub);

        $this->assertEquals(
            [
                20 => [],
                10 => []
            ],
            $evaluateReturn
        );
    }
}
