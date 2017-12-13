:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

:: REMEMBER TO UPDATE THE BASH FILE

SET UNIT_COVERAGE_THRESHOLD=20

@echo ===============================UNIT TESTS===============================
@echo off
call vendor\bin\phpunit --configuration dev\tests\phpunit.xml --testsuite unit --coverage-clover clover.xml

@echo ===========================UNIT TEST COVERAGE===========================
@echo off
call vendor\bin\coverage-check clover.xml %UNIT_COVERAGE_THRESHOLD%

@echo ===========================VERIFICATION TESTS===========================
@echo off
call vendor\bin\phpunit --configuration dev\tests\phpunit.xml --testsuite verification
