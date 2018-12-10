:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

:: REMEMBER TO UPDATE THE BASH FILE

@echo off
@echo ===============================PHP CODE SNIFFER REPORT===============================
call vendor\bin\phpcs --standard=.\dev\tests\static\Magento --ignore=src/Magento/FunctionalTestingFramework/Group,src/Magento/FunctionalTestingFramework/AcceptanceTester.php .\src
call vendor\bin\phpcs --standard=.\dev\tests\static\Magento .\dev\tests\unit
call vendor\bin\phpcs --standard=.\dev\tests\static\Magento --ignore=dev/tests/verification/_generated .\dev\tests\verification

@echo ===============================COPY PASTE DETECTOR REPORT===============================
call vendor\bin\phpcpd .\src

@echo ===============================PHP MESS DETECTOR REPORT===============================
call vendor\bin\phpmd --exclude _generated,src\Magento\FunctionalTestingFramework\Group,src\Magento\FunctionalTestingFramework\AcceptanceTester.php .\src text \dev\tests\static\Magento\CodeMessDetector\ruleset.xml

@echo ===============================MAGENTO COPYRIGHT REPORT===============================
call bin\copyright-check.bat
