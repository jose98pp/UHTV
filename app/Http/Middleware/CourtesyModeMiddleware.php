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
        $active = filter_var(SiteSetting::value('courtesy_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
        $auto = filter_var(SiteSetting::value('courtesy_auto', '1'), FILTER_VALIDATE_BOOLEAN);
        $dueDate = SiteSetting::value('billing_due_date');
        $stage = (int) SiteSetting::value('courtesy_stage', '1');

        if ($auto && $dueDate && now()->startOfDay()->gt(\Illuminate\Support\Carbon::parse($dueDate)->startOfDay())) {
            $days = \Illuminate\Support\Carbon::parse($dueDate)->startOfDay()->diffInDays(now()->startOfDay());
            $stage = $days >= 8 ? 3 : ($days >= 4 ? 2 : 1);
            $active = true;
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
