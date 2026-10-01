@echo off
echo ====================================================
echo Starting POV Indian Application Servers...
echo ====================================================

:: Start MySQL (XAMPP) if not running
powershell -Command "$t = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue; if (-not $t.TcpTestSucceeded) { if (Test-Path 'D:\xampp\mysql_start.bat') { Start-Process 'D:\xampp\mysql_start.bat' -WindowStyle Minimized } elseif (Test-Path 'C:\xampp\mysql_start.bat') { Start-Process 'C:\xampp\mysql_start.bat' -WindowStyle Minimized } }"

:: Start Node.js API Backend on Port 5000
start "POV Indian - Node.js Backend" cmd /k "cd /d %~dp0backend && npm start"

:: Start PHP Built-in Web Server on Port 8000
start "POV Indian - PHP Frontend" cmd /k "cd /d %~dp0 && php -S 127.0.0.1:8000"

echo.
echo ====================================================
echo Servers successfully started!
echo - Main Website:  http://127.0.0.1:8000
echo - Backend API:   http://127.0.0.1:5000
echo - Health Check:  http://127.0.0.1:5000/api/health
echo ====================================================
timeout /t 2 >nul
start http://127.0.0.1:8000

