<?php

namespace App\Http\Controllers;

use App\Core\PluginManager;
use App\Models\Plugin;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function __construct(protected PluginManager $manager) {}

    public function index()
    {
        $installed = Plugin::orderBy('name')->get()->keyBy('slug');

        $registry       = $this->manager->fetchRegistry();
        $registryBySlug = collect($registry)->keyBy('slug');

        $latestVersions     = $registryBySlug->map(fn($item) => $item['version']);
        $latestDownloadUrls = $registryBySlug->map(fn($item) => $item['download_url'] ?? '');

        return view('plugins.index', compact('installed', 'registry', 'latestVersions', 'latestDownloadUrls'));
    }

    public function install(Request $request)
    {
        $request->validate([
            'github_url'   => 'required|url',
            'download_url' => 'required|url',
        ]);

        $result = $this->manager->installFromZip(
            $request->github_url,
            $request->download_url
        );

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function activate(Plugin $plugin)
    {
        $result = $this->manager->activate($plugin->slug);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        // Full redirect agar plugin boot normal di request berikutnya
        return redirect()->route('plugins.index')->with('success', $result['message']);
    }

    public function deactivate(Plugin $plugin)
    {
        $result = $this->manager->deactivate($plugin->slug);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function update(Request $request, Plugin $plugin)
    {
        $request->validate(['download_url' => 'required|url']);

        $result = $this->manager->update($plugin->slug, $request->download_url);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function uninstall(Request $request, Plugin $plugin)
    {
        $removeData = $request->boolean('remove_data', false);
        $result     = $this->manager->uninstall($plugin->slug, $removeData);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}