<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatisticService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardStatisticService $dashboardStatisticService)
    {
    }

    public function index(): View
    {
        $stats = $this->dashboardStatisticService->adminStats();

        return view('admin.dashboard', compact('stats'));
    }
}
