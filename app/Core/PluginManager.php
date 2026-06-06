<?php

namespace App\Core;

use App\Models\Plugin;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;

class PluginManager
{
    protected string $pluginsPath;

    public function __construct()
    {
        $this->pluginsPath = base_path('plugins');
    }

    /**
     * Load semua plugin yang aktif — dipanggil saat Laravel boot
     */
    public function loadActive(): void
    {
        if (!app()->bound('db') || !$this->tableExists()) {
            return;
        }

        try {
            Plugin::where('is_active', true)->each(function (Plugin $plugin) {
                $this->loadPlugin($plugin->slug);
            });
        } catch (\Exception $e) {
            // Silent fail saat boot — jangan sampai crash seluruh app
        }
    }

    /**
     * Load satu plugin ke dalam Laravel
     */
    public function loadPlugin(string $slug): bool
    {
        $entryFile = base_path("plugins/{$slug}/Plugin.php");

        if (!File::exists($entryFile)) {
            return false;
        }

        require_once $entryFile;

        // Try slug as-is first (existing plugins: accounting, inventory, etc.)
        // Then try PascalCase (new plugins with hyphens: sales-order → SalesOrder)
        $candidates = [
            "Plugins\\{$slug}\\Plugin",
            "Plugins\\" . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $slug))) . "\\Plugin",
        ];

        foreach ($candidates as $className) {
            if (class_exists($className)) {
                app()->register($className);
                return true;
            }
        }

        return false;
    }

    /**
     * Install plugin dari GitHub
     */
    /**
     * Install plugin dari ZIP URL (tidak butuh Git di mesin user)
     */
    public function installFromZip(string $githubUrl, string $downloadUrl): array
    {
        $slug = $this->parseSlugFromUrl($githubUrl);

        if (!$slug) {
            return ['success' => false, 'message' => 'URL GitHub tidak valid.'];
        }

        if (Plugin::where('slug', $slug)->exists()) {
            return ['success' => false, 'message' => "Plugin '{$slug}' sudah terinstall."];
        }

        $result = $this->downloadAndExtract($slug, $downloadUrl);

        if (!$result['success']) {
            return $result;
        }

        $targetPath = base_path("plugins/{$slug}");
        $manifest   = $this->readManifest($slug);

        if (!$manifest) {
            File::deleteDirectory($targetPath);
            return ['success' => false, 'message' => 'plugin.json tidak ditemukan di dalam ZIP.'];
        }

        Plugin::updateOrCreate(
            ['slug' => $slug],
            [
                'name'           => $manifest['name'] ?? $slug,
                'version'        => $manifest['version'] ?? '1.0.0',
                'description'    => $manifest['description'] ?? null,
                'author'         => $manifest['author'] ?? null,
                'github_url'     => $githubUrl,
                'installed_path' => $targetPath,
                'is_active'      => false,
                'installed_at'   => now(),
            ]
        );

        return ['success' => true, 'message' => "Plugin '{$manifest['name']}' berhasil diinstall. Silakan activate."];
    }

    /**
     * Download ZIP, extract, pindah ke plugins/{slug}/
     * GitHub ZIP selalu punya nested folder: repo-tagname/
     */
    protected function downloadAndExtract(string $slug, string $downloadUrl): array
    {
        $targetPath = base_path("plugins/{$slug}");

        try {
            $client = new \GuzzleHttp\Client(['timeout' => 60, 'verify' => false]);
            $response = $client->get($downloadUrl, ['allow_redirects' => true]);
            $zipContent = $response->getBody()->getContents();
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal download ZIP: ' . $e->getMessage()];
        }

        $tmpZip = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "erp-plugin-{$slug}-" . time() . '.zip';
        file_put_contents($tmpZip, $zipContent);

        $zip = new \ZipArchive();
        if ($zip->open($tmpZip) !== true) {
            unlink($tmpZip);
            return ['success' => false, 'message' => 'File ZIP tidak valid.'];
        }

        $tmpExtract = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "erp-plugin-{$slug}-" . time();
        $zip->extractTo($tmpExtract);
        $zip->close();
        unlink($tmpZip);

        // GitHub ZIP: isi ada di subfolder repo-tagname/
        $subfolders = glob($tmpExtract . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
        if (empty($subfolders)) {
            File::deleteDirectory($tmpExtract);
            return ['success' => false, 'message' => 'Struktur ZIP tidak dikenali.'];
        }

        if (File::exists($targetPath)) {
            File::deleteDirectory($targetPath);
        }

        File::moveDirectory($subfolders[0], $targetPath);
        File::deleteDirectory($tmpExtract);

        return ['success' => true];
    }

    /**
     * Resolve migration path — cek dua lokasi konvensional
     */
    protected function getMigrationPath(string $slug): ?string
    {
        $candidates = [
            base_path("plugins/{$slug}/migrations"),
            base_path("plugins/{$slug}/database/migrations"),
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Aktivasi plugin
     */
    public function activate(string $slug): array
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return ['success' => false, 'message' => 'Plugin tidak ditemukan.'];
        }

        // Jalankan migrations plugin
        $migrationPath = $this->getMigrationPath($slug);
        if ($migrationPath) {
            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $migrationPath);
            Artisan::call('migrate', [
                '--path'  => $relativePath,
                '--force' => true,
            ]);
        }

        // Seed permissions dari plugin.json
        $manifest = $this->readManifest($slug);
        if (!empty($manifest['permissions'])) {
            foreach ($manifest['permissions'] as $perm) {
                Permission::firstOrCreate(['name' => $perm['name'], 'guard_name' => 'web']);
            }
        }

        // Load plugin sekarang juga
        $loaded = $this->loadPlugin($slug);

        if (!$loaded) {
            return ['success' => false, 'message' => 'Gagal load plugin. Cek file Plugin.php.'];
        }

        $plugin->update(['is_active' => true]);

        return ['success' => true, 'message' => "Plugin '{$plugin->name}' berhasil diaktifkan."];
    }

    /**
     * Deaktivasi plugin
     */
    public function deactivate(string $slug): array
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return ['success' => false, 'message' => 'Plugin tidak ditemukan.'];
        }

        $plugin->update(['is_active' => false]);

        return ['success' => true, 'message' => "Plugin '{$plugin->name}' dinonaktifkan. Berlaku setelah restart."];
    }

    /**
     * Update plugin via ZIP download (tidak butuh Git)
     */
    public function update(string $slug, string $downloadUrl): array
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return ['success' => false, 'message' => 'Plugin tidak ditemukan.'];
        }

        $result = $this->downloadAndExtract($slug, $downloadUrl);

        if (!$result['success']) {
            return $result;
        }

        $manifest = $this->readManifest($slug);
        if ($manifest) {
            Plugin::where('slug', $slug)->update([
                'version' => $manifest['version'] ?? '1.0.0',
            ]);
        }

        $newVersion = $manifest['version'] ?? '?';
        return ['success' => true, 'message' => "Plugin '{$plugin->name}' berhasil diupdate ke v{$newVersion}."];
    }

    /**
     * Uninstall plugin
     */
    public function uninstall(string $slug, bool $removeData = false): array
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return ['success' => false, 'message' => 'Plugin tidak ditemukan.'];
        }

        // Rollback migrations kalau removeData = true
        if ($removeData) {
            $migrationPath = $this->getMigrationPath($slug);
            if ($migrationPath) {
                $migrations = File::files($migrationPath);

                \Schema::disableForeignKeyConstraints();

                foreach (array_reverse($migrations) as $migration) {
                    $migrationName = pathinfo($migration->getFilename(), PATHINFO_FILENAME);

                    try {
                        // Panggil down() langsung — urutan drop FK sudah benar
                        $instance = require $migration->getPathname();
                        if (method_exists($instance, 'down')) {
                            $instance->down();
                        }
                    } catch (\Exception $e) {
                        // silent
                    }

                    \DB::table('migrations')
                        ->where('migration', $migrationName)
                        ->delete();
                }

                \Schema::enableForeignKeyConstraints();
            }
        }

        // Hapus folder
        $pluginPath = base_path("plugins/{$slug}");
        if (File::exists($pluginPath)) {
            if (PHP_OS_FAMILY === 'Windows') {
                exec("rd /s /q \"{$pluginPath}\"");
            } else {
                exec("rm -rf {$pluginPath}");
            }
        }

        $plugin->delete();

        $message = $removeData
            ? "Plugin '{$plugin->name}' diuninstall dan semua data dihapus."
            : "Plugin '{$plugin->name}' diuninstall. Data tetap tersimpan.";

        return ['success' => true, 'message' => $message];
    }

    /**
     * Baca plugin.json
     */
    public function readManifest(string $slug): ?array
    {
        $manifestPath = base_path("plugins/{$slug}/plugin.json");

        if (!File::exists($manifestPath)) {
            return null;
        }

        return json_decode(File::get($manifestPath), true);
    }

    /**
     * Parse slug dari GitHub URL
     */
    protected function parseSlugFromUrl(string $url): ?string
    {
        // https://github.com/user/erp-plugin-hr → hr
        // https://github.com/user/plugin-inventory → inventory
        preg_match('/github\.com\/[^\/]+\/(?:erp-plugin-|plugin-)?(.+?)(?:\.git)?$/', $url, $matches);
        return $matches[1] ?? null;
    }

    protected function gitBin(): string
    {
        return PHP_OS_FAMILY === 'Windows'
            ? 'C:\\Program Files\\Git\\cmd\\git.exe'
            : 'git';
    }

    protected function tableExists(): bool
    {
        try {
            return \Schema::hasTable('plugins');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fetchRegistry(): array
    {
        $registryUrl = config('plugins.registry_url',
            'https://raw.githubusercontent.com/febriandto/erp-plugin-registry/main/registry.json'
        );

        try {
            $client   = new \GuzzleHttp\Client([
                'timeout'         => 5,
                'verify'          => false, // bypass SSL issue di local
            ]);
            $response = $client->get($registryUrl);
            $data     = json_decode($response->getBody(), true);
            return $data['plugins'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Cek apakah plugin sudah terinstall
     */
    public function isInstalled(string $slug): bool
    {
        return Plugin::where('slug', $slug)->exists();
    }
}