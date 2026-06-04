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
        $plugins = Plugin::orderBy('name')->get();
        return view('plugins.index', compact('plugins'));
    }

    public function install(Request $request)
    {
        $request->validate([
            'github_url' => 'required|url',
        ]);

        $result = $this->manager->installFromGithub($request->github_url);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function activate(Plugin $plugin)
    {
        $result = $this->manager->activate($plugin->slug);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function deactivate(Plugin $plugin)
    {
        $result = $this->manager->deactivate($plugin->slug);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function update(Plugin $plugin)
    {
        $result = $this->manager->update($plugin->slug);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function uninstall(Plugin $plugin)
    {
        $result = $this->manager->uninstall($plugin->slug);
        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}