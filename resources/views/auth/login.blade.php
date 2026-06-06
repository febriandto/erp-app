<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Login — ERP System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
</head>
<body class="antialiased d-flex flex-column">
<div class="page page-center">
    <div class="container container-tight py-4">

        <div class="text-center mb-4">
            <h1 class="fw-bold fs-2">ERP System</h1>
        </div>

        <div class="card card-md anim-fadein">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Masuk ke akun Anda</h2>

                @if($errors->has('login'))
                <div class="alert alert-danger alert-dismissible mb-3">
                    {{ $errors->first('login') }}
                    <a class="btn-close" data-bs-dismiss="alert"></a>
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST"
                      x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email atau Username</label>
                        <input type="text" name="login" value="{{ old('login') }}"
                               class="form-control @error('login') is-invalid @enderror"
                               placeholder="email@perusahaan.com atau username" autofocus required autocomplete="username">
                        @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                               class="form-control" placeholder="Password" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input">
                            <span class="form-check-label">Ingat saya</span>
                        </label>
                    </div>

                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <span x-show="!loading">
                                <i class="ti ti-login me-1"></i>Masuk
                            </span>
                            <span x-show="loading" x-cloak>
                                <span class="spinner-border spinner-border-sm me-1"></span>Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center text-muted mt-3">
            ERP System &copy; {{ date('Y') }}
        </div>

    </div>
</div>
</body>
</html>
