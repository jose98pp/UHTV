<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingPayment;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $settings = [
            'concept' => SiteSetting::value('billing_concept', 'Desarrollo y creación de Última Hora TV'),
            'pending_amount' => SiteSetting::value('billing_pending_amount', '0'),
            'currency' => SiteSetting::value('billing_currency', 'BOB'),
            'due_date' => SiteSetting::value('billing_due_date'),
            'courtesy_enabled' => SiteSetting::value('courtesy_enabled', '0'),
            'courtesy_auto' => SiteSetting::value('courtesy_auto', '1'),
            'courtesy_stage' => SiteSetting::value('courtesy_stage', '1'),
            'courtesy_message' => SiteSetting::value('courtesy_message', 'El servicio de desarrollo y mantenimiento de este sitio tiene pagos pendientes.'),
        ];
        $payments = BillingPayment::with('creator')->latest('paid_at')->paginate(15);
        return view('admin.billing.index', compact('settings', 'payments'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'concept' => ['required', 'string', 'max:180'],
            'pending_amount' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'currency' => ['required', 'in:BOB,USD'],
            'due_date' => ['nullable', 'date'],
            'courtesy_auto' => ['required', 'boolean'],
            'courtesy_stage' => ['required', 'integer', 'between:1,3'],
            'courtesy_message' => ['required', 'string', 'max:1000'],
        ]);

        foreach (['concept' => 'billing_concept', 'pending_amount' => 'billing_pending_amount', 'currency' => 'billing_currency', 'due_date' => 'billing_due_date', 'courtesy_auto' => 'courtesy_auto', 'courtesy_stage' => 'courtesy_stage', 'courtesy_message' => 'courtesy_message'] as $field => $key) {
            SiteSetting::put($key, $data[$field] ?? '');
        }
        return back()->with('success', 'Configuración de facturación guardada.');
    }

    public function toggleCourtesy(Request $request)
    {
        $enabled = $request->validate(['enabled' => ['required', 'boolean']])['enabled'];
        SiteSetting::put('courtesy_enabled', $enabled ? '1' : '0');
        return back()->with('success', $enabled ? 'Modo de cortesía activado.' : 'Sitio restaurado a su apariencia normal.');
    }

    public function recordPayment(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            $currency = SiteSetting::value('billing_currency', 'BOB');
            BillingPayment::create($data + ['currency' => $currency, 'created_by' => auth()->id()]);
            $balance = max(0, (float) SiteSetting::value('billing_pending_amount', '0') - (float) $data['amount']);
            SiteSetting::put('billing_pending_amount', number_format($balance, 2, '.', ''));
            if ($balance <= 0) {
                SiteSetting::put('courtesy_enabled', '0');
            }
        });

        return back()->with('success', 'Pago registrado. El saldo pendiente fue actualizado.');
    }
}
