<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CourtesyModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = $request->is('admin*');
        $active = false;
        $stage = 1;
        $message = 'El servicio de desarrollo y mantenimiento de este sitio tiene pagos pendientes.';
        $dueDate = null;
        $notice = ['pending' => false, 'amount' => '0', 'currency' => 'BOB', 'due_date' => null];

        try {
            $auto = filter_var(SiteSetting::value('courtesy_auto', '1'), FILTER_VALIDATE_BOOLEAN);
            $dueDate = SiteSetting::value('billing_due_date');
            $stage = (int) SiteSetting::value('courtesy_stage', '1');
            $override = SiteSetting::value('courtesy_override', 'auto');

            // El control manual prevalece siempre: "off" restaura el sitio incluso si venció.
            if ($override === 'on') {
                $active = true;
            } elseif ($override === 'off') {
                $active = false;
            } elseif ($auto && $dueDate && Carbon::parse($dueDate)->startOfDay()->lt(now()->startOfDay())) {
                $active = true;
                $days = Carbon::parse($dueDate)->startOfDay()->diffInDays(now()->startOfDay());
                $stage = $days >= 8 ? 3 : ($days >= 4 ? 2 : 1);
            }

            $message = SiteSetting::value('courtesy_message', $message);
            $amount = SiteSetting::value('billing_pending_amount', '0');
            $notice = [
                'pending' => (float) $amount > 0,
                'amount' => $amount,
                'currency' => SiteSetting::value('billing_currency', 'BOB'),
                'due_date' => $dueDate,
            ];
        } catch (\Throwable $e) {
            // Si las migraciones aún no se aplicaron, nunca interrumpir la web pública.
            Log::warning('No se pudo cargar la configuración de cortesía UHTV.', ['error' => $e->getMessage()]);
        }

        view()->share('courtesyMode', [
            'active' => !$isAdmin && $active,
            'stage' => max(1, min(3, $stage)),
            'message' => $message,
        ]);
        view()->share('adminBillingNotice', $notice);

        return $next($request);
    }
}
