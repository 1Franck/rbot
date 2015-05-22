@echo off

rem -------------------------------------------------------------
rem  RBot !
rem -------------------------------------------------------------

@setlocal

set RBOT_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%RBOT_PATH%rbcli" %*

@endlocal