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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is($item['active'] ?? '') ? 'active' : '' }}"
                           href="{{ $item['url'] }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>
                            <span class="nav-link-title">{{ $item['title'] }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </aside>

    <div class="page-wrapper">

        {{-- TOP: Horizontal navbar — daftar modul --}}
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
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
                    <div class="ms-auto">
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
                    </div>
                </div>
            </div>
        </header>

        {{-- Page header --}}
        <div class="page-header d-print-none">
            <div class="container-xl">
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
            <div class="container-xl">

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
            <div class="container-xl">
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
</body>
</html>
