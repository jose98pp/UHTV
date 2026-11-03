@extends('layouts.admin')
@section('title', 'Usuarios y roles - UHTV')
@section('page-title', 'Usuarios y roles')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div><h3 class="mb-1">Gestión de usuarios</h3><p class="text-muted mb-0">Cuentas, roles y estado de acceso.</p></div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Crear usuario</a>
    </div>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Último acceso</th><th></th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td><td>{{ $user->email }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
                    <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Activo' : 'Desactivado' }}</span></td>
                    <td>{{ $user->last_login_at?->diffForHumans() ?? 'Sin registro' }}</td>
                    <td class="text-end">
                        @if($user->role !== 'superadmin')
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario? Sus noticias se conservarán.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                        @else <span class="text-muted small">Cuenta protegida</span> @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay usuarios registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection
