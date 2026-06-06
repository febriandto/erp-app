<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AppUpdate extends Command
{
    protected $signature = 'app:update';
    protected $description = 'Update aplikasi: git pull, submodule sync, migrate, clear cache';

    public function handle(): int
    {
        $this->info('Memulai update aplikasi...');
        $this->newLine();

        // 1. Git pull
        $this->components->task('Mengambil update terbaru (git pull)', function () {
            exec('git pull origin main 2>&1', $output, $code);
            if ($code !== 0) {
                $this->newLine();
                foreach ($output as $line) $this->line("  $line");
                return false;
            }
            return true;
        });

        // 2. Submodule update
        $this->components->task('Sync plugin submodule', function () {
            exec('git submodule update --init --recursive 2>&1', $output, $code);
            return $code === 0;
        });

        // 3. Migrate
        $this->components->task('Menjalankan migrasi database', function () {
            Artisan::call('migrate', ['--force' => true]);
            return true;
        });

        // 4. View cache clear
        $this->components->task('Membersihkan view cache', function () {
            Artisan::call('view:clear');
            return true;
        });

        // 5. App cache clear
        $this->components->task('Membersihkan application cache', function () {
            Artisan::call('cache:clear');
            return true;
        });

        $this->newLine();
        $this->components->success('Aplikasi berhasil diupdate!');

        return self::SUCCESS;
    }
}
