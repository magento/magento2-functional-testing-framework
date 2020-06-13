:: Copyright © Magento, Inc. All rights reserved.
:: See COPYING.txt for license details.

@echo off
SETLOCAL EnableDelayedExpansion
SET BLOCKLIST_FILE=bin/blocklist.txt
SET i=0

FOR /F %%x IN ('git ls-tree --full-tree -r --name-only HEAD') DO (
    SET GOOD_EXT=
    if "%%~xx"==".php" set GOOD_EXT=1
    if "%%~xx"==".xml" set GOOD_EXT=1
    if "%%~xx"==".xsd" set GOOD_EXT=1
    IF DEFINED GOOD_EXT (
        SET BLOCKLISTED=
        FOR /F "tokens=* skip=5" %%f IN (%BLOCKLIST_FILE%) DO (
            SET LINE=%%x
            IF NOT "!LINE!"=="!LINE:%%f=!" (
                SET BLOCKLISTED=1
            )
        )
        IF NOT DEFINED BLOCKLISTED (
            FIND "Copyright © Magento, Inc. All rights reserved." %%x >nul
            IF ERRORLEVEL 1 (
                SET /A i+=1
                SET NO_COPYRIGHT_LIST[!i!]=%%x
            )
        )
    )
)

IF DEFINED NO_COPYRIGHT_LIST[1] (
    ECHO THE FOLLOWING FILES ARE MISSING THE MAGENTO COPYRIGHT:
    ECHO.
    ECHO     Copyright © Magento, Inc. All rights reserved.
    ECHO     See COPYING.txt for license details.
    ECHO.
    FOR /L %%a IN (1,1,%i%) DO (
        ECHO !NO_COPYRIGHT_LIST[%%a]!
    )
)