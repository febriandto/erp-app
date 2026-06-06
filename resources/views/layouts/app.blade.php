<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'ERP System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body class="antialiased">
<div class="wrapper">

    {{-- LEFT: Vertical sidebar — brand + sub-menu modul aktif --}}
    @unless(request()->is('/') || request()->is('dashboard'))
    <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark">
                <a href="/">ERP System</a>
            </h1>
            <div class="collapse navbar-collapse" id="sidebar-menu">
                <ul class="navbar-nav pt-lg-3">
                    @foreach($sidebarItems as $item)
                    @if(!empty($item['children']))
                    {{-- Collapsible section --}}
                    @php $sectionOpen = request()->is($item['active'] ?? ''); @endphp
                    <li class="nav-item">
                        <a class="nav-link {{ $sectionOpen ? '' : 'collapsed' }}"
                           href="#sidebar-{{ \Illuminate\Support\Str::slug($item['title']) }}"
                           data-bs-toggle="collapse" role="button"
                           aria-expanded="{{ $sectionOpen ? 'true' : 'false' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>
                            <span class="nav-link-title">{{ $item['title'] }}</span>
                            <span class="nav-link-toggle"></span>
                        </a>
                        <div class="collapse {{ $sectionOpen ? 'show' : '' }}"
                             id="sidebar-{{ \Illuminate\Support\Str::slug($item['title']) }}">
                            <ul class="navbar-nav navbar-nav-sub">
                                @foreach($item['children'] as $sub)
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is($sub['active'] ?? '') ? 'active' : '' }}"
                                       href="{{ $sub['url'] }}">
                                        {{ $sub['title'] }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @else
                    {{-- Flat link --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is($item['active'] ?? '') ? 'active' : '' }}"
                           href="{{ $item['url'] ?? '#' }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>
                            <span class="nav-link-title">{{ $item['title'] }}</span>
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>
    @endunless

    <div class="page-wrapper">

        {{-- TOP: Horizontal navbar — daftar modul --}}
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}"
                               href="/">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-home"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>
                        @foreach($menuItems as $item)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is($item['active']) ? 'active' : '' }}"
                               href="{{ $item['url'] }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <span class="nav-link-title">{{ $item['title'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if(auth()->user()->hasRole('admin'))
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('admin/plugins*') ? 'active' : '' }}"
                                   href="{{ route('plugins.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-puzzle"></i>
                                    </span>
                                    <span class="nav-link-title">Plugins</span>
                                </a>
                            </li>
                        </ul>
                        @endif

                        {{-- User dropdown --}}
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex align-items-center gap-2 px-2"
                               data-bs-toggle="dropdown">
                                <span class="avatar avatar-sm"
                                      style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206bc4&color=fff)">
                                </span>
                                <span class="d-none d-md-inline text-body-secondary small">
                                    {{ auth()->user()->name }}
                                </span>
                                <i class="ti ti-chevron-down text-muted" style="font-size:.75rem"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header">
                                    <div class="fw-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                                    @if(auth()->user()->roles->isNotEmpty())
                                    <div class="mt-1">
                                        @foreach(auth()->user()->roles as $role)
                                        <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ti ti-logout me-2"></i>Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page header --}}
        <div class="page-header d-print-none">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        @if(isset($breadcrumbs))
                        <div class="page-pretitle">{{ $breadcrumbs }}</div>
                        @endif
                        <h2 class="page-title">
                            @yield('page-title', 'Dashboard')
                        </h2>
                    </div>
                    @hasSection('page-actions')
                    <div class="col-auto ms-auto d-print-none">
                        @yield('page-actions')
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Page body --}}
        <div class="page-body">
            <div class="container-fluid">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible mb-3" role="alert">
                    <div>{{ session('success') }}</div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                    <div>{{ session('error') }}</div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
                @endif

                @yield('content')

            </div>
        </div>

        {{-- Footer --}}
        <footer class="footer footer-transparent d-print-none">
            <div class="container-fluid">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                ERP System &copy; {{ date('Y') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>

    </div>
</div>
@stack('scripts')
</body>
</html>
