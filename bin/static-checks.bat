:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

:: REMEMBER TO UPDATE THE BASH FILE

@echo off
@echo ===============================PHP CODE SNIFFER REPORT===============================
call vendor\bin\phpcs .\src --standard=.\dev\tests\static\Magento
call vendor\bin\phpcs .\dev\tests\unit --standard=.\dev\tests\static\Magento
call vendor\bin\phpcs .\dev\tests\verification --standard=.\dev\tests\static\Magento --ignore=dev\tests\verification\_generated

@echo ===============================COPY PASTE DETECTOR REPORT===============================
call vendor\bin\phpcpd .\src

@echo "===============================PHP MESS DETECTOR REPORT===============================
vendor\bin\phpmd .\src text \dev\tests\static\Magento\CodeMessDetector\ruleset.xml --exclude _generated

@echo ===============================MAGENTO COPYRIGHT REPORT===============================
echo msgbox "INFO:Copyright check currently not run as part of .bat implementation" > "%temp%\popup.vbs"
wscript.exe "%temp%\popup.vbs"
::bin\copyright-check
