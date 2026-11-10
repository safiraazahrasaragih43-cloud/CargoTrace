@echo off
cd /d "%~dp0"
set "PATH=C:\php8.2;%PATH%"
echo Starting CargoTrace Laravel Dev Server with PHP 8.2...
php artisan serve --host=127.0.0.1 --port=8000
pause
