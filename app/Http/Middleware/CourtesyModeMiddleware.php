<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourtesyModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAdmin = $request->is('admin*');
        $auto = filter_var(SiteSetting::value('courtesy_auto', '1'), FILTER_VALIDATE_BOOLEAN);
        $dueDate = SiteSetting::value('billing_due_date');
        $stage = (int) SiteSetting::value('courtesy_stage', '1');
        $override = SiteSetting::value('courtesy_override', 'auto');

        // El control manual siempre prevalece sobre la automatización.
        if ($override === 'on') {
            $active = true;
        } elseif ($override === 'off') {
            $active = false;
        } else {
            $active = $auto && $dueDate && now()->startOfDay()->gt(\\Illuminate\\Support\\Carbon::parse($dueDate)->startOfDay());
            if ($active) {
                $days = \\Illuminate\\Support\\Carbon::parse($dueDate)->startOfDay()->diffInDays(now()->startOfDay());
                $stage = $days >= 8 ? 3 : ($days >= 4 ? 2 : 1);
            }
        }

        $mode = [
            'active' => !$isAdmin && $active,
            'stage' => max(1, min(3, $stage)),
            'message' => SiteSetting::value('courtesy_message', 'El servicio de desarrollo y mantenimiento de este sitio tiene pagos pendientes.'),
        ];

        view()->share('courtesyMode', $mode);
        view()->share('adminBillingNotice', [
            'pending' => (float) SiteSetting::value('billing_pending_amount', '0') > 0,
            'amount' => SiteSetting::value('billing_pending_amount', '0'),
            'currency' => SiteSetting::value('billing_currency', 'BOB'),
            'due_date' => $dueDate,
        ]);

        return $next($request);
    }
}
