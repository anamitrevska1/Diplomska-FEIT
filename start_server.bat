@echo off
setlocal enabledelayedexpansion

set COLOR_RED=31
set COLOR_GREEN=32

echo Starting PHP server
start /b php artisan serve

echo Starting [ npm run dev ]
npm run dev

echo Server Stopped

:echoColor
for /f "tokens=*" %%A in ('echo prompt $e^|cmd') do set "esc=%%A"
echo %esc%[%~1m%~2%esc%[0m
exit /b

endlocal
