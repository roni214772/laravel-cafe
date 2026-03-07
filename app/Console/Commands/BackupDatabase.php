<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature   = 'db:backup';
    protected $description = 'Veritabanının günlük yedeğini alır (son 7 yedek saklanır)';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $filename = 'db_' . now()->format('Y-m-d_H-i');
        $conn     = config('database.default');

        if ($conn === 'sqlite') {
            $src  = database_path('database.sqlite');
            $dest = $dir . DIRECTORY_SEPARATOR . $filename . '.sqlite';
            if (! File::exists($src)) {
                $this->error('SQLite dosyası bulunamadı: ' . $src);
                return self::FAILURE;
            }
            File::copy($src, $dest);
            $this->info("✓ SQLite yedeği alındı: storage/app/backups/{$filename}.sqlite");
        } else {
            // MySQL / MariaDB — mysqldump ile
            $db   = config('database.connections.mysql.database');
            $user = config('database.connections.mysql.username');
            $pass = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $dest = $dir . DIRECTORY_SEPARATOR . $filename . '.sql';

            // mysqldump yolunu bul
            $dump = 'mysqldump';
            if (file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
                $dump = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';
            }

            $passArg = $pass ? "-p" . escapeshellarg($pass) : '';
            $cmd = "{$dump} -h {$host} -u {$user} {$passArg} {$db} > " . escapeshellarg($dest) . " 2>&1";
            exec($cmd, $output, $code);

            if ($code !== 0) {
                $this->error('mysqldump başarısız: ' . implode(PHP_EOL, $output));
                return self::FAILURE;
            }
            $this->info("✓ MySQL yedeği alındı: storage/app/backups/{$filename}.sql");
        }

        // Sadece son 7 yedeği tut
        $backups = collect(File::files($dir))
            ->filter(fn($f) => str_starts_with($f->getFilename(), 'db_'))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->values();

        $backups->slice(7)->each(function ($f) {
            File::delete($f->getPathname());
            $this->line('  Eski yedek silindi: ' . $f->getFilename());
        });

        $this->info('Toplam yedek: ' . min($backups->count(), 7));

        return self::SUCCESS;
    }
}
