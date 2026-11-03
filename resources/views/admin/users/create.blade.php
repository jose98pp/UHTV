@extends('layouts.admin')
@section('title', 'Crear usuario - UHTV')
@section('page-title', 'Crear usuario')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form method="POST" action="{{ route('admin.users.store') }}">@csrf
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nombre</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="col-md-6"><label class="form-label">Rol</label><select name="role" class="form-select" required>@foreach($roles as $role)<option value="{{ $role }}" @selected(old('role')===$role)>{{ ucfirst($role) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Contraseña inicial</label><input type="password" name="password" class="form-control" minlength="8" required></div>
            <div class="col-md-6"><label class="form-label">Confirmar contraseña</label><input type="password" name="password_confirmation" class="form-control" required></div>
        </div>
        <div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Crear usuario</button><a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a></div>
    </form>
</div></div>
@endsection
