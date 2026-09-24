@echo off
title UHTV - Servidor Laravel
cd /d "%~dp0\UHTV"
if not exist "artisan" cd /d "%~dp0"
echo ==============================================
echo   Iniciando Servidor Laravel UHTV
echo   Accede en tu navegador a:
echo   http://127.0.0.1:8000
echo ==============================================
"C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan serve --host=127.0.0.1 --port=8000
pause
