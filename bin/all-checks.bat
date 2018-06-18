:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

@echo off
call bin\static-checks.bat

@echo off
call bin\phpunit-checks.bat
