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

:: MySQL (XAMPP) baslat - zaten calisiyor olabilir
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
  if exist "C:\xampp\mysql\bin\mysqld.exe" (
    echo MySQL baslatiliyor...
    start /MIN "MySQL" "C:\xampp\mysql\bin\mysqld.exe"
    echo MySQL'in hazir olması bekleniyor...
    :wait_mysql
    "%PHP%" -r "try { new PDO('mysql:host=127.0.0.1;port=3307', 'root', ''); echo 'OK'; } catch(Exception $e) { exit(1); }" >nul 2>&1
    if errorlevel 1 (
      timeout /t 1 /nobreak >nul
      goto wait_mysql
    )
    echo MySQL hazir!
  ) else (
    echo MySQL bulunamadi! XAMPP kurulu oldugundan emin olun.
    pause
    exit /b 1
  )
) else (
  echo MySQL zaten calisiyor.
)

:: Laravel sunucusu
start "Cafe POS" /MIN "%PHP%" artisan serve --host=0.0.0.0 --port=8000

:: Reverb WebSocket sunucusu (anlık mutfak guncellemesi)
start "Cafe Reverb" /MIN "%PHP%" artisan reverb:start --host=0.0.0.0 --port=8080

:: Zamanlayici (her dakika calisir, gece 02:00'de otomatik yedek alir)
start "Cafe Scheduler" /MIN "%PHP%" artisan schedule:work

:: POS Cihaz Koprusu (Node.js)
where node >nul 2>&1
if not errorlevel 1 (
  if exist "pos-bridge\server.js" (
    if not exist "pos-bridge\node_modules" (
      echo POS Bridge bagimliliklari yukleniyor...
      cd pos-bridge
      call npm install --silent >nul 2>&1
      cd ..
    )
    start "POS Bridge" /MIN node pos-bridge\server.js
    echo POS Cihaz Koprusu baslatildi (port 3457)
  )
) else (
  echo Node.js bulunamadi - POS koprusu atlanıyor
)

timeout /t 6 /nobreak >nul
start "" "http://localhost:8000/adisyon"
