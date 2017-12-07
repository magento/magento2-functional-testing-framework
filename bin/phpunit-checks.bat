:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

@echo ===============================UNIT TESTS===============================
@echo off
call vendor\bin\phpunit --configuration dev\tests\phpunit.xml --testsuite unit --coverage-xml build\coverage-xml

@echo off
@echo ===============================VERIFICATION TESTS===============================
call vendor\bin\phpunit --configuration dev\tests\phpunit.xml --testsuite verification --coverage-xml build\coverage-xml