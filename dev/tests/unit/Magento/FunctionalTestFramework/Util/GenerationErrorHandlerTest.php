<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use AspectMock\Test as AspectMock;
use tests\unit\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

/**
 * Class GenerationErrorHandlerTest
 */
class GenerationErrorHandlerTest extends MagentoTestCase
{
    /**
     * Test get errors when all errors are distinct
     */
    public function testGetDistinctErrors()
    {
        AspectMock::double(
            MftfApplicationConfig::class,
            ['getPhase' => MftfApplicationConfig::GENERATION_PHASE]
        );

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
    public function testGetErrorsWithSameKey()
    {
        AspectMock::double(
            MftfApplicationConfig::class,
            ['getPhase' => MftfApplicationConfig::GENERATION_PHASE]
        );

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
    public function testGetAllErrorsDuplicate()
    {
        AspectMock::double(
            MftfApplicationConfig::class,
            ['getPhase' => MftfApplicationConfig::GENERATION_PHASE]
        );

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
     * Test reset
     */
    public function testResetError()
    {
        AspectMock::double(
            MftfApplicationConfig::class,
            ['getPhase' => MftfApplicationConfig::GENERATION_PHASE]
        );

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

    public function tearDown(): void
    {
        $property = new \ReflectionProperty(GenerationErrorHandler::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}
