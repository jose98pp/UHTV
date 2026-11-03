@extends('layouts.admin')
@section('title', 'Editar usuario - UHTV')
@section('page-title', 'Editar usuario')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name',$user->name) }}" required></div>
            <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" required></div>
            <div class="col-md-6"><label class="form-label">Rol</label><select name="role" class="form-select">@foreach($roles as $role)<option value="{{ $role }}" @selected(old('role',$user->role)===$role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Estado</label><select name="is_active" class="form-select"><option value="1" @selected(old('is_active',(int)$user->is_active) == 1)>Activo</option><option value="0" @selected(old('is_active',(int)$user->is_active) == 0)>Desactivado</option></select></div>
            <div class="col-md-6"><label class="form-label">Nueva contraseña (opcional)</label><input type="password" name="password" class="form-control" autocomplete="new-password"></div>
            <div class="col-md-6"><label class="form-label">Confirmar nueva contraseña</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
        </div>
        <p class="text-muted small mt-3">Deja los campos de contraseña vacíos si no deseas cambiarla.</p>
        <div class="d-flex gap-2"><button class="btn btn-primary">Guardar cambios</button><a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a></div>
    </form>
</div></div>
@endsection
