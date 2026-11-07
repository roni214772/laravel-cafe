@echo off
cd /d "%~dp0"

set "PHP="
for %%P in (
  "%~dp0tools\php\php.exe"
  "C:\Users\Roni\Downloads\php-8.5.12-1-src\php.exe"
  "C:\Users\Roni\Downloads\php-8.5.12-1-nts-Win32-vs17-x64\php.exe"
  "C:\Users\Roni\Downloads\php-8.4.0-1-nts-Win32-vs17-x64\php.exe"
  "C:\xampp\php\php.exe"
) do (
  if not defined PHP if exist "%%~P" set "PHP=%%~P"
)

if not defined PHP (
  echo PHP 8.4 veya daha yeni bir surum bulunamadi.
  echo PHP 8.4+ kurup bu dosyadaki PHP yollarindan birine yerlestirin.
  pause
  exit /b 1
)

for %%I in ("%PHP%") do set "PHPRC=%%~dpIphp.ini"

"%PHP%" -r "exit(PHP_VERSION_ID >= 80400 ? 0 : 1);"
if errorlevel 1 (
  echo PHP 8.4 veya daha yeni bir surum gerekli.
  pause
  exit /b 1
)

if not exist "vendor\autoload.php" (
  echo Laravel paketleri kurulu degil. Once composer install calistirin.
  pause
  exit /b 1
)

if not exist "public\build\manifest.json" (
  echo Frontend dosyalari derlenmemis. Once npm run build calistirin.
  pause
  exit /b 1
)

start "Cafe POS" /MIN "%PHP%" artisan serve --host=0.0.0.0 --port=8000
start "Cafe Reverb" /MIN "%PHP%" artisan reverb:start --host=0.0.0.0 --port=8080
start "Cafe Scheduler" /MIN "%PHP%" artisan schedule:work

where node >nul 2>&1
if not errorlevel 1 (
  if exist "pos-bridge\server.js" (
    if not exist "pos-bridge\node_modules" (
      echo POS Bridge bagimliliklari yukleniyor...
      pushd pos-bridge
      call npm install
      if errorlevel 1 (
        echo POS Bridge bagimliliklari yuklenemedi.
        popd
        pause
        exit /b 1
      )
      popd
    )
    start "POS Bridge" /MIN node pos-bridge\server.js
    echo POS Cihaz Koprusu baslatildi.
  )
) else (
  echo Node.js bulunamadi - POS koprusu baslatilmayacak.
)

timeout /t 6 /nobreak >nul
start "" "http://localhost:8000/adisyon"
