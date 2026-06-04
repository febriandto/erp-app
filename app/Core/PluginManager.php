<?php

namespace App\Core;

use App\Models\Plugin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

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

        $className = "Plugins\\{$slug}\\Plugin";

        if (!class_exists($className)) {
            return false;
        }

        app()->register($className);
        return true;
    }

    /**
     * Install plugin dari GitHub
     */
    public function installFromGithub(string $githubUrl): array
    {
        // Parse slug dari URL github
        // https://github.com/user/erp-plugin-hr → hr
        $slug = $this->parseSlugFromUrl($githubUrl);

        if (!$slug) {
            return ['success' => false, 'message' => 'URL GitHub tidak valid.'];
        }

        $targetPath = base_path("plugins/{$slug}");

        // Cek sudah terinstall
        if (File::exists($targetPath)) {
            return ['success' => false, 'message' => "Plugin '{$slug}' sudah terinstall."];
        }

        // Git clone
        $command = "git clone {$githubUrl} {$targetPath} 2>&1";
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            return [
                'success' => false,
                'message' => 'Git clone gagal: ' . implode("\n", $output),
            ];
        }

        // Baca plugin.json
        $manifest = $this->readManifest($slug);

        if (!$manifest) {
            // Hapus folder kalau manifest tidak ada
            File::deleteDirectory($targetPath);
            return ['success' => false, 'message' => 'plugin.json tidak ditemukan.'];
        }

        // Simpan ke database
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

        return ['success' => true, 'message' => "Plugin '{$manifest['name']}' berhasil diinstall."];
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
        $migrationPath = base_path("plugins/{$slug}/migrations");
        if (File::exists($migrationPath)) {
            Artisan::call('migrate', [
                '--path'  => "plugins/{$slug}/migrations",
                '--force' => true,
            ]);
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
     * Update plugin via git pull
     */
    public function update(string $slug): array
    {
        $pluginPath = base_path("plugins/{$slug}");

        if (!File::exists($pluginPath)) {
            return ['success' => false, 'message' => 'Plugin tidak terinstall.'];
        }

        $command = "git -C {$pluginPath} pull 2>&1";
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            return ['success' => false, 'message' => 'Git pull gagal: ' . implode("\n", $output)];
        }

        // Update versi di database
        $manifest = $this->readManifest($slug);
        if ($manifest) {
            Plugin::where('slug', $slug)->update([
                'version' => $manifest['version'] ?? '1.0.0',
            ]);
        }

        return ['success' => true, 'message' => "Plugin '{$slug}' berhasil diupdate."];
    }

    /**
     * Uninstall plugin
     */
    public function uninstall(string $slug): array
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return ['success' => false, 'message' => 'Plugin tidak ditemukan.'];
        }

        // Hapus folder
        $pluginPath = base_path("plugins/{$slug}");
        if (File::exists($pluginPath)) {
            File::deleteDirectory($pluginPath);
        }

        // Hapus dari database
        $plugin->delete();

        return ['success' => true, 'message' => "Plugin '{$plugin->name}' berhasil diuninstall."];
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

    protected function tableExists(): bool
    {
        try {
            return \Schema::hasTable('plugins');
        } catch (\Exception $e) {
            return false;
        }
    }
}