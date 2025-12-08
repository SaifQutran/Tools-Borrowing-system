@echo off
SETLOCAL

:: 1. Get the local IP address
:: The 'for /f' command parses the output of 'ipconfig' to find the IPv4 Address
:: It looks for the line containing "IPv4 Address" and extracts the address after the colon.
echo Getting your local IPv4 address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4 Address"') do (
    SET "LOCAL_IP=%%a"
)

:: Clean up the IP string (removes leading spaces)
SET "LOCAL_IP=%LOCAL_IP: =%"

:: Check if an IP address was successfully retrieved
if "%LOCAL_IP%"=="" (
    echo Error: Could not determine local IP address.
    echo Please check your network connection.
    pause
    EXIT /B 1
)

:: 2. Construct the URL and display the command
SET "APP_PORT=8000"
SET "APP_URL=http://%LOCAL_IP%:%APP_PORT%"
echo.
echo Detected IP: **%LOCAL_IP%**
echo Starting Laravel server at: **%APP_URL%**
echo.

:: 3. Open the URL in the default browser (using start)
start "" "%APP_URL%"

:: 4. Execute the php artisan serve command
:: This must be the last command, as 'php artisan serve' blocks the console
php artisan serve --host %LOCAL_IP% --port %APP_PORT%

ENDLOCAL
pause