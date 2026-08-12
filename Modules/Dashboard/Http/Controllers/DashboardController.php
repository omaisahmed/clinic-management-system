<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    public function index(): View
    {
        Gate::authorize('dashboard.view');

        return view('dashboard::index', [
            'overview' => $this->dashboard->overview(),
            'todaySchedule' => $this->dashboard->todaySchedule(),
            'waitingQueue' => $this->dashboard->waitingQueue(),
            'upcomingAppointments' => $this->dashboard->upcomingAppointments(),
            'recentPatients' => $this->dashboard->recentPatients(),
            'recentPayments' => $this->dashboard->recentPayments(),
            'revenueByDay' => $this->dashboard->revenueByDay(),
            'appointmentsByStatus' => $this->dashboard->appointmentsByStatus(),
        ]);
    }
}
