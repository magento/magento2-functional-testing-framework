<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;

/**
 * Class GenerationErrorHandlerTest
 */
class GenerationErrorHandlerTest extends MagentoTestCase
{
    /**
     * Test get errors when all errors are distinct
     */
    public function testGetDistinctErrors():void
    {
        $expectedAllErrors = [
            'test' => [
                'Sameple1Test' => [
                    'message' => 'TestError1',
                    'generated' => false,
                ],
                'Sameple2Test' => [
                    'message' => 'TestError2',
                    'generated' => true,
                ]
            ],
            'suite' => [
                'Sameple1Suite' => [
                    'message' => 'SuiteError1',
                    'generated' => false,
                ],
            ]
        ];

        $expectedTestErrors = [
            'Sameple1Test' => [
                'message' => 'TestError1',
                'generated' => false,
            ],
            'Sameple2Test' => [
                'message' => 'TestError2',
                'generated' => true,
            ]
        ];

        $expectedSuiteErrors = [
            'Sameple1Suite' => [
                'message' => 'SuiteError1',
                'generated' => false,
            ],
        ];

        GenerationErrorHandler::getInstance()->addError('test', 'Sameple1Test', 'TestError1');
        GenerationErrorHandler::getInstance()->addError('test', 'Sameple2Test', 'TestError2', true);
        GenerationErrorHandler::getInstance()->addError('suite', 'Sameple1Suite', 'SuiteError1');

        // Assert getAllErrors
        $this->assertEquals($expectedAllErrors, GenerationErrorHandler::getInstance()->getAllErrors());
        // Assert getErrorsByType
        $this->assertEquals($expectedTestErrors, GenerationErrorHandler::getInstance()->getErrorsByType('test'));
        $this->assertEquals($expectedSuiteErrors, GenerationErrorHandler::getInstance()->getErrorsByType('suite'));
    }

    /**
     * Test get errors when some errors have the same key
     */
    public function testGetErrorsWithSameKey(): void
    {
        $expectedAllErrors = [
            'test' => [
                'Sameple1Test' => [
                    'message' => [
                        0 => 'TestError1',
                        1 => 'TestError3'
                    ],
                    'generated' => [
                        0 => false,
                        1 => true
                    ],
                ],
                'Sameple2Test' => [
                    'message' => 'TestError2',
                    'generated' => true,
                ],
            ],
            'suite' => [
                'Sameple1Suite' => [
                    'message' => [
                        0 => 'SuiteError1',
                        1 => 'SuiteError2',
                    ],
                    'generated' => [
                        0 => false,
                        1 => false,
                    ],
                ],
            ],
        ];

        $expectedTestErrors = [
            'Sameple1Test' => [
                'message' => [
                    0 => 'TestError1',
                    1 => 'TestError3'
                ],
                'generated' => [
                    0 => false,
                    1 => true
                ],
            ],
            'Sameple2Test' => [
                'message' => 'TestError2',
                'generated' => true,
            ],
        ];

        $expectedSuiteErrors = [
            'Sameple1Suite' => [
                'message' => [
                    0 => 'SuiteError1',
                    1 => 'SuiteError2',
                ],
                'generated' => [
                    0 => false,
                    1 => false,
                ],
            ],
        ];

        GenerationErrorHandler::getInstance()->addError('test', 'Sameple1Test', 'TestError1');
        GenerationErrorHandler::getInstance()->addError('suite', 'Sameple1Suite', 'SuiteError1');
        GenerationErrorHandler::getInstance()->addError('test', 'Sameple2Test', 'TestError2', true);
        GenerationErrorHandler::getInstance()->addError('suite', 'Sameple1Suite', 'SuiteError2');
        GenerationErrorHandler::getInstance()->addError('test', 'Sameple1Test', 'TestError3', true);

        // Assert getAllErrors
        $this->assertEquals($expectedAllErrors, GenerationErrorHandler::getInstance()->getAllErrors());
        // Assert getErrorsByType
        $this->assertEquals($expectedTestErrors, GenerationErrorHandler::getInstance()->getErrorsByType('test'));
        $this->assertEquals($expectedSuiteErrors, GenerationErrorHandler::getInstance()->getErrorsByType('suite'));
    }

    /**
     * Test get errors when some errors are duplicate
     */
    public function testGetAllErrorsDuplicate(): void
    {
        $expectedAllErrors = [
            'test' => [
                'Sameple1Test' => [
                    'message' => [
                        0 => 'TestError1',
                        1 => 'TestError1'
                    ],
                    'generated' => [
                        0 => false,
                        1 => false
                    ],
                ],
                'Sameple2Test' => [
                    'message' => 'TestError2',
                    'generated' => true,
                ],
            ],
            'suite' => [
                'Sameple1Suite' => [
                    'message' => [
                        0 => 'SuiteError1',
                        1 => 'SuiteError2',
                    ],
                    'generated' => [
                        0 => false,
                        1 => false,
                    ],
                ],
            ],
        ];

        $expectedTestErrors = [
            'Sameple1Test' => [
                'message' => [
                    0 => 'TestError1',
                    1 => 'TestError1'
                ],
                'generated' => [
                    0 => false,
                    1 => false
                ],
            ],
            'Sameple2Test' => [
                'message' => 'TestError2',
                'generated' => true,
            ],
        ];

        $expectedSuiteErrors = [
            'Sameple1Suite' => [
                'message' => [
                    0 => 'SuiteError1',
                    1 => 'SuiteError2',
                ],
                'generated' => [
                    0 => false,
                    1 => false,
                ],
            ],
        ];

        GenerationErrorHandler::getInstance()->addError('test', 'Sameple1Test', 'TestError1');
        GenerationErrorHandler::getInstance()->addError('suite', 'Sameple1Suite', 'SuiteError1');
        GenerationErrorHandler::getInstance()->addError('test', 'Sameple2Test', 'TestError2', true);
        GenerationErrorHandler::getInstance()->addError('suite', 'Sameple1Suite', 'SuiteError2');
        GenerationErrorHandler::getInstance()->addError('test', 'Sameple1Test', 'TestError1');

        // Assert getAllErrors
        $this->assertEquals($expectedAllErrors, GenerationErrorHandler::getInstance()->getAllErrors());
        // Assert getErrorsByType
        $this->assertEquals($expectedTestErrors, GenerationErrorHandler::getInstance()->getErrorsByType('test'));
        $this->assertEquals($expectedSuiteErrors, GenerationErrorHandler::getInstance()->getErrorsByType('suite'));
    }

    /**
     * Test get all error messages
     *
     * @param string $expectedErrMessages
     * @param array  $errors
     *
     * @return void
     * @dataProvider getAllErrorMessagesDataProvider
     */
    public function testGetAllErrorMessages(string $expectedErrMessages, array $errors): void
    {
        $handler = GenerationErrorHandler::getInstance();
        $handler->reset();

        $property = new ReflectionProperty(GenerationErrorHandler::class, 'errors');
        $property->setAccessible(true);
        $property->setValue($handler, $errors);

        // Assert getAllErrorMessages
        $this->assertEquals($expectedErrMessages, GenerationErrorHandler::getInstance()->getAllErrorMessages());
    }

    /**
     * Data provider for testGetAllErrorMessages()
     *
     * @return array
     */
    public function getAllErrorMessagesDataProvider(): array
    {
        return [
            ['', []],
            ['', [
                    'test' => [],
                    'suite' => [],
                ]
            ],
            ['TestError1'
                . PHP_EOL
                . 'TestError2'
                . PHP_EOL
                . 'TestError3'
                . PHP_EOL
                . 'SuiteError1'
                . PHP_EOL
                . 'SuiteError2'
                . PHP_EOL
                . 'SuiteError3'
                . PHP_EOL
                . 'SuiteError4',
                [
                    'test' => [
                        'Sameple1Test' => [
                            'message' => [
                                0 => 'TestError1',
                                1 => 'TestError2'
                            ],
                            'generated' => [
                                0 => false,
                                1 => false
                            ],
                        ],
                        'Sameple2Test' => [
                            'message' => 'TestError3',
                            'generated' => true,
                        ],
                    ],
                    'suite' => [
                        'Sameple1Suite' => [
                            'message' => 'SuiteError1',
                            'generated' => true,
                        ],
                        'Sameple2Suite' => [
                            'message' => [
                                0 => 'SuiteError2',
                                1 => 'SuiteError3',
                                2 => 'SuiteError4',
                            ],
                            'generated' => [
                                0 => false,
                                1 => true,
                                2 => false,
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }

    /**
     * Test reset
     */
    public function testResetError(): void
    {
        GenerationErrorHandler::getInstance()->addError('something', 'some', 'error');
        GenerationErrorHandler::getInstance()->addError('otherthing', 'other', 'error');
        GenerationErrorHandler::getInstance()->reset();

        // Assert getAllErrors
        $this->assertEquals([], GenerationErrorHandler::getInstance()->getAllErrors());
        // Assert getErrorsByType
        $this->assertEquals([], GenerationErrorHandler::getInstance()->getErrorsByType('something'));
        $this->assertEquals([], GenerationErrorHandler::getInstance()->getErrorsByType('otherthing'));
        $this->assertEquals([], GenerationErrorHandler::getInstance()->getErrorsByType('nothing'));
    }

    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        $property = new ReflectionProperty(GenerationErrorHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }
}
