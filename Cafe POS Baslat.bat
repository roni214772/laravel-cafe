@echo off
cd /d "%~dp0"
set PHP=php
where php >nul 2>&1 || (
  if exist "D:\Program Files\php\php.exe" (set "PHP=D:\Program Files\php\php.exe") else (
  if exist "C:\php\php.exe" (set PHP=C:\php\php.exe) else (
  if exist "C:\Program Files\php\php.exe" (set "PHP=C:\Program Files\php\php.exe") else (
  echo PHP bulunamadi! & pause & exit /b 1))))

:: Onceki surecleri kapat
taskkill /F /IM php.exe >nul 2>&1
timeout /t 1 /nobreak >nul

:: MySQL (XAMPP) baslat - zaten calisiyor olabilir, hata olsa da devam et
sc query mysql >nul 2>&1
if errorlevel 1 (
  if exist "C:\xampp\mysql\bin\mysqld.exe" (
    echo MySQL baslatiliyor...
    start /MIN "MySQL" "C:\xampp\mysql\bin\mysqld.exe"
    timeout /t 3 /nobreak >nul
  )
) else (
  net start mysql >nul 2>&1
  timeout /t 2 /nobreak >nul
)

:: Laravel sunucusu
start "Cafe POS" /MIN "%PHP%" artisan serve --host=0.0.0.0 --port=8000

:: Reverb WebSocket sunucusu (anlık mutfak guncellemesi)
start "Cafe Reverb" /MIN "%PHP%" artisan reverb:start --host=0.0.0.0 --port=8080

:: Zamanlayici (her dakika calisir, gece 02:00'de otomatik yedek alir)
start "Cafe Scheduler" /MIN "%PHP%" artisan schedule:work

timeout /t 6 /nobreak >nul
start "" "http://localhost:8000/adisyon"
