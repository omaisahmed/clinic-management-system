<?php

declare(strict_types=1);

namespace Modules\Audit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(): View
    {
        Gate::authorize('audit_logs.view');

        $logs = AuditLog::query()
            ->with('user')
            ->when(request('action'), fn ($q) => $q->where('action', request('action')))
            ->when(request('module'), fn ($q) => $q->where('module', request('module')))
            ->when(request('date'), fn ($q) => $q->whereDate('created_at', request('date')))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('audit::index', [
            'logs' => $logs,
        ]);
    }
}
