@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('page-actions')
    <a href="{{ route('users.roles.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Add Role
    </a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Users</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($roles as $role)
                <tr>
                    <td>
                        <span class="badge bg-blue-lt fs-6 fw-medium">{{ $role->name }}</span>
                    </td>
                    <td class="text-muted">{{ $role->users_count }} user</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('users.roles.edit', $role) }}">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                @if($role->users_count === 0)
                                <div class="dropdown-divider"></div>
                                <button class="dropdown-item text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-delete-{{ $role->id }}">
                                    <i class="ti ti-trash me-2"></i>Hapus
                                </button>
                                @endif
                            </div>
                        </div>

                        @if($role->users_count === 0)
                        <div class="modal modal-blur fade" id="modal-delete-{{ $role->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="modal-title">Hapus Role</div>
                                        <div class="text-muted mt-1">Yakin hapus role <strong>{{ $role->name }}</strong>?</div>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('users.roles.destroy', $role) }}" method="POST" class="w-100"
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
                    <td colspan="3" class="text-center text-muted py-4">Belum ada role.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
