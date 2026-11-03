@extends('layouts.admin')
@section('title', 'Facturación y control del sitio - UHTV')
@section('page-title', 'Facturación y control del sitio')
@section('content')
<div class="container-fluid">
    <div class="alert alert-info"><strong>Control exclusivo del Superadministrador.</strong> Los cambios se aplican al sitio público y no eliminan contenido.</div>
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <h4 class="mb-3">Configuración del servicio</h4>
        <form method="POST" action="{{ route('admin.billing.update') }}">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Concepto</label><input name="concept" class="form-control" value="{{ old('concept',$settings['concept']) }}" required></div>
                <div class="col-md-3"><label class="form-label">Saldo pendiente</label><input type="number" min="0" step="0.01" name="pending_amount" class="form-control" value="{{ old('pending_amount',$settings['pending_amount']) }}" required></div>
                <div class="col-md-3"><label class="form-label">Moneda</label><select name="currency" class="form-select"><option value="BOB" @selected($settings['currency']==='BOB')>BOB (Bs)</option><option value="USD" @selected($settings['currency']==='USD')>USD ($)</option></select></div>
                <div class="col-md-4"><label class="form-label">Fecha de vencimiento</label><input type="date" name="due_date" class="form-control" value="{{ $settings['due_date'] }}"></div>
                <div class="col-md-4"><label class="form-label">Etapa manual</label><select name="courtesy_stage" class="form-select">@foreach([1,2,3] as $stage)<option value="{{ $stage }}" @selected((int)$settings['courtesy_stage']===$stage)>Etapa {{ $stage }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Avance automático</label><select name="courtesy_auto" class="form-select"><option value="1" @selected($settings['courtesy_auto']=='1')>Activado según vencimiento</option><option value="0" @selected($settings['courtesy_auto']=='0')>Solo control manual</option></select></div>
                <div class="col-12"><label class="form-label">Mensaje público</label><textarea name="courtesy_message" rows="2" class="form-control" required>{{ old('courtesy_message',$settings['courtesy_message']) }}</textarea></div>
            </div>
            <button class="btn btn-primary mt-3">Guardar configuración</button>
        </form>
    </div></div>
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <h4>Modo de cortesía y restauración</h4>
        <p class="text-muted">La activación manual puede detenerse en cualquier momento. Al desactivar, la web vuelve al diseño normal.</p>
        <div class="d-flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('admin.billing.courtesy') }}">@csrf
                <input type="hidden" name="enabled" value="1"><button class="btn btn-warning">Activar modo de cortesía</button>
            </form>
            <form method="POST" action="{{ route('admin.billing.courtesy') }}">@csrf
                <input type="hidden" name="enabled" value="0"><button class="btn btn-success">Restaurar página normal</button>
            </form>
        </div>
    </div></div>
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <h4>Registrar pago</h4>
        <form method="POST" action="{{ route('admin.billing.payments') }}">@csrf
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Importe recibido</label><input type="number" name="amount" min="0.01" step="0.01" class="form-control" required></div>
                <div class="col-md-3"><label class="form-label">Fecha de pago</label><input type="datetime-local" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
                <div class="col-md-3"><label class="form-label">Referencia</label><input name="reference" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Observaciones</label><input name="notes" class="form-control"></div>
            </div>
            <button class="btn btn-outline-primary mt-3">Registrar pago</button>
        </form>
    </div></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-4"><h4>Historial de pagos</h4>
        <div class="table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Importe</th><th>Referencia</th><th>Registrado por</th></tr></thead><tbody>
        @forelse($payments as $payment)<tr><td>{{ $payment->paid_at->format('d/m/Y H:i') }}</td><td>{{ $payment->currency }} {{ number_format($payment->amount,2) }}</td><td>{{ $payment->reference ?: '—' }}</td><td>{{ $payment->creator->name ?? 'Sistema' }}</td></tr>@empty<tr><td colspan="4" class="text-muted text-center">Todavía no hay pagos registrados.</td></tr>@endforelse
        </tbody></table></div>{{ $payments->links() }}
    </div></div>
</div>
@endsection
