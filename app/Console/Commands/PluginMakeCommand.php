<?php

namespace App\Console\Commands;

use App\Models\Plugin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PluginMakeCommand extends Command
{
    protected $signature = 'plugin:make {slug : Plugin slug, e.g. sales-order}';
    protected $description = 'Scaffold a new plugin with full boilerplate';

    public function handle(): int
    {
        $rawSlug    = $this->argument('slug');
        $slug       = Str::slug($rawSlug);
        $namespace  = Str::studly($slug);
        $modelName  = $namespace;
        $ctrlName   = $namespace . 'Controller';
        $tableName  = Str::snake(Str::plural($namespace));
        $permPrefix = $slug;

        $displayName = $this->ask('Display name', Str::title(str_replace('-', ' ', $slug)));
        $description = $this->ask('Description', "{$displayName} management module");
        $author      = $this->ask('Author', 'febriandto');
        $icon        = $this->ask('Tabler icon (e.g. ti ti-shopping-cart)', 'ti ti-puzzle');
        $order       = $this->ask('Menu order', '100');

        $targetPath = base_path("plugins/{$slug}");

        if (File::exists($targetPath)) {
            $this->error("Plugin '{$slug}' already exists.");
            return self::FAILURE;
        }

        $this->newLine();
        $this->line("  <fg=cyan>Slug</>       : {$slug}");
        $this->line("  <fg=cyan>Namespace</>  : Plugins\\{$namespace}");
        $this->line("  <fg=cyan>Model</>      : {$modelName}");
        $this->line("  <fg=cyan>Table</>      : {$tableName}");
        $this->newLine();

        foreach ([
            $targetPath,
            "{$targetPath}/Controllers",
            "{$targetPath}/Models",
            "{$targetPath}/migrations",
            "{$targetPath}/resources/views",
        ] as $dir) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $ts    = now()->format('Y_m_d_His');
        $files = [
            "{$targetPath}/plugin.json"
                => $this->makePluginJson($slug, $displayName, $description, $author, $permPrefix),
            "{$targetPath}/Plugin.php"
                => $this->makePluginPhp($namespace, $slug, $displayName, $icon, $order, $permPrefix),
            "{$targetPath}/routes.php"
                => $this->makeRoutes($namespace, $slug, $ctrlName),
            "{$targetPath}/Controllers/{$ctrlName}.php"
                => $this->makeController($namespace, $ctrlName, $modelName, $slug, $permPrefix),
            "{$targetPath}/Models/{$modelName}.php"
                => $this->makeModel($namespace, $modelName, $tableName),
            "{$targetPath}/migrations/{$ts}_create_{$tableName}_table.php"
                => $this->makeMigration($tableName),
            "{$targetPath}/resources/views/index.blade.php"
                => $this->makeViewIndex($displayName, $slug, $modelName),
            "{$targetPath}/resources/views/create.blade.php"
                => $this->makeViewForm($displayName, $slug, 'create'),
            "{$targetPath}/resources/views/edit.blade.php"
                => $this->makeViewForm($displayName, $slug, 'edit'),
        ];

        foreach ($files as $path => $content) {
            File::put($path, $content);
            $short = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
            $this->line("  <fg=green>✓</> {$short}");
        }

        // Register ke DB supaya muncul di Plugin Manager
        $this->components->task('Register plugin ke database', function () use ($slug, $displayName, $description, $author, $targetPath) {
            if (!\Schema::hasTable('plugins')) return false;

            Plugin::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'           => $displayName,
                    'version'        => '1.0.0',
                    'description'    => $description,
                    'author'         => $author,
                    'installed_path' => $targetPath,
                    'is_active'      => false,
                    'installed_at'   => now(),
                ]
            );
            return true;
        });

        $this->newLine();
        $this->components->success("Plugin <fg=cyan>{$displayName}</> scaffolded!");
        $this->newLine();
        $this->line('  <fg=yellow>Next steps:</>');
        $this->line("  1. Edit <fg=cyan>plugins/{$slug}/plugin.json</> — sesuaikan depends jika butuh plugin lain");
        $this->line("  2. <fg=cyan>composer85 dump-autoload</>");
        $this->line("  3. Activate via Plugin Manager di browser");
        $this->newLine();

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function makePluginJson(string $slug, string $name, string $desc, string $author, string $perm): string
    {
        return json_encode([
            'name'        => $name,
            'slug'        => $slug,
            'version'     => '1.0.0',
            'description' => $desc,
            'author'      => $author,
            'depends'     => [],
            'permissions' => [
                ['name' => "{$perm}.view",   'label' => "View {$name}"],
                ['name' => "{$perm}.manage", 'label' => "Manage {$name}"],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    private function makePluginPhp(string $ns, string $slug, string $name, string $icon, string $order, string $perm): string
    {
        return <<<PHP
<?php

namespace Plugins\\{$ns};

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        \$this->loadMigrationsFrom(__DIR__ . '/migrations');

        if (app()->runningInConsole()) return;

        \$this->loadViewsFrom(__DIR__ . '/resources/views', '{$slug}');

        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        \$this->app->booted(function () {
            app()->make(MenuManager::class)->add([
                'title'      => '{$name}',
                'url'        => route('{$slug}.index'),
                'icon'       => '{$icon}',
                'order'      => {$order},
                'active'     => '{$slug}*',
                'permission' => '{$perm}.view',
                'children'   => [
                    [
                        'title'      => '{$name}',
                        'icon'       => '{$icon}',
                        'active'     => '{$slug}/list*',
                        'permission' => '{$perm}.view',
                        'children'   => [
                            ['title' => 'All Records',    'url' => route('{$slug}.index'),  'active' => '{$slug}/list'],
                            ['title' => 'Create',         'url' => route('{$slug}.create'), 'active' => '{$slug}/list/create', 'permission' => '{$perm}.manage'],
                        ],
                    ],
                ],
            ]);
        });
    }
}
PHP;
    }

    private function makeRoutes(string $ns, string $slug, string $ctrlName): string
    {
        return <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use Plugins\\{$ns}\\Controllers\\{$ctrlName};

Route::prefix('{$slug}')->name('{$slug}.')->group(function () {
    Route::get('list',               [{$ctrlName}::class, 'index'])->name('index')->middleware('can:{$slug}.view');
    Route::get('list/create',        [{$ctrlName}::class, 'create'])->name('create')->middleware('can:{$slug}.manage');
    Route::post('list',              [{$ctrlName}::class, 'store'])->name('store')->middleware('can:{$slug}.manage');
    Route::get('list/{record}',      [{$ctrlName}::class, 'show'])->name('show')->middleware('can:{$slug}.view');
    Route::get('list/{record}/edit', [{$ctrlName}::class, 'edit'])->name('edit')->middleware('can:{$slug}.manage');
    Route::put('list/{record}',      [{$ctrlName}::class, 'update'])->name('update')->middleware('can:{$slug}.manage');
    Route::delete('list/{record}',   [{$ctrlName}::class, 'destroy'])->name('destroy')->middleware('can:{$slug}.manage');
});
PHP;
    }

    private function makeController(string $ns, string $ctrlName, string $modelName, string $slug, string $perm): string
    {
        return <<<PHP
<?php

namespace Plugins\\{$ns}\\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\\{$ns}\\Models\\{$modelName};

class {$ctrlName} extends Controller
{
    public function index()
    {
        \$records = {$modelName}::latest()->paginate(20);
        return view('{$slug}::index', compact('records'));
    }

    public function create()
    {
        return view('{$slug}::create');
    }

    public function store(Request \$request)
    {
        \$request->validate([
            'name' => 'required|string|max:255',
        ]);

        {$modelName}::create(\$request->only('name'));

        return redirect()->route('{$slug}.index')
            ->with('success', 'Record berhasil dibuat.');
    }

    public function show({$modelName} \$record)
    {
        return view('{$slug}::show', compact('record'));
    }

    public function edit({$modelName} \$record)
    {
        return view('{$slug}::edit', compact('record'));
    }

    public function update(Request \$request, {$modelName} \$record)
    {
        \$request->validate([
            'name' => 'required|string|max:255',
        ]);

        \$record->update(\$request->only('name'));

        return redirect()->route('{$slug}.index')
            ->with('success', 'Record berhasil diupdate.');
    }

    public function destroy({$modelName} \$record)
    {
        \$record->delete();
        return redirect()->route('{$slug}.index')
            ->with('success', 'Record berhasil dihapus.');
    }
}
PHP;
    }

    private function makeModel(string $ns, string $modelName, string $tableName): string
    {
        return <<<PHP
<?php

namespace Plugins\\{$ns}\\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table    = '{$tableName}';
    protected \$fillable = ['name'];
}
PHP;
    }

    private function makeMigration(string $tableName): string
    {
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            // TODO: tambah kolom sesuai kebutuhan
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;
    }

    private function makeViewIndex(string $name, string $slug, string $modelName): string
    {
        return <<<BLADE
@extends('layouts.app')

@section('title', '{$name}')
@section('page-title', '{$name}')

@section('page-actions')
@can('{$slug}.manage')
<a href="{{ route('{$slug}.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add
</a>
@endcan
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse(\$records as \$record)
                <tr>
                    <td class="text-muted">{{ \$record->id }}</td>
                    <td>{{ \$record->name }}</td>
                    <td class="text-muted">{{ \$record->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @can('{$slug}.manage')
                                <a href="{{ route('{$slug}.edit', \$record) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                <form action="{{ route('{$slug}.destroy', \$record) }}" method="POST"
                                      onsubmit="return confirm('Hapus record ini?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(\$records->hasPages())
    <div class="card-footer d-flex justify-content-end">
        {{ \$records->links() }}
    </div>
    @endif
</div>
@endsection
BLADE;
    }

    private function makeViewForm(string $name, string $slug, string $mode): string
    {
        $isEdit   = $mode === 'edit';
        $title    = $isEdit ? "Edit {$name}" : "Add {$name}";
        $action   = $isEdit ? "route('{$slug}.update', \$record)" : "route('{$slug}.store')";
        $method   = $isEdit ? '@csrf @method(\'PUT\')' : '@csrf';
        $btnLabel = $isEdit ? 'Update' : 'Simpan';
        // Pre-compute old() call to avoid PHP heredoc interpolating ternary expressions
        $oldValue = $isEdit ? "old('name', \$record->name)" : "old('name')";

        return <<<BLADE
@extends('layouts.app')

@section('title', '{$title}')
@section('page-title', '{$title}')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card anim-fadein">
            <div class="card-header">
                <h3 class="card-title">{$title}</h3>
            </div>
            <form action="{{ {$action} }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                {$method}
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name"
                               value="{{ {$oldValue} }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>
                        @error('name')<div class="invalid-feedback">{{ \$message }}</div>@enderror
                    </div>
                    {{-- TODO: tambah field lain sesuai kebutuhan --}}
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>{$btnLabel}</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('{$slug}.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE;
    }
}
