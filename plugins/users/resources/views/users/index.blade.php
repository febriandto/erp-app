@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('page-actions')
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Add User
    </a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Joined</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm"
                                  style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=206bc4&color=fff)">
                            </span>
                            <div>
                                <div class="fw-medium">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <div class="text-muted small">Anda</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge bg-blue-lt">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse
                    </td>
                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                @if($user->id !== auth()->id())
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-delete-{{ $user->id }}">
                                    <i class="ti ti-trash me-2"></i>Hapus
                                </button>
                                @endif
                            </div>
                        </div>

                        {{-- Modal Delete --}}
                        @if($user->id !== auth()->id())
                        <div class="modal modal-blur fade" id="modal-delete-{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="modal-title">Hapus User</div>
                                        <div class="text-muted mt-1">Yakin hapus <strong>{{ $user->name }}</strong>? Aksi ini tidak bisa dibatalkan.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-100"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100 mb-2">
                                                <span x-show="!loading"><i class="ti ti-trash me-1"></i>Ya, Hapus</span>
                                                <span x-show="loading" x-cloak>
                                                    <span class="spinner-border spinner-border-sm me-1"></span>Menghapus...
                                                </span>
                                            </button>
                                            <button type="button" class="btn w-100 text-muted" data-bs-dismiss="modal">Batal</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
