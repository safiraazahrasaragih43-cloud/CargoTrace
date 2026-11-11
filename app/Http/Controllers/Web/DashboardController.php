<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\EtaService;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected EtaService $etaService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // Paginated shipments scoped to user
        $shipments = $this->shipmentService->getShipments($user, $request->only(['search', 'status']), 10);

        // Operational stats
        $baseQuery = Shipment::query();
        if ($user->isCustomer()) {
            $baseQuery->where('customer_id', $user->id);
        }

        $totalActive = (clone $baseQuery)->whereNotIn('status', Shipment::TERMINAL_STATUSES)->count();
        $totalInTransit = (clone $baseQuery)->where('status', Shipment::STATUS_IN_TRANSIT)->count();
        $totalDelivered = (clone $baseQuery)->where('status', Shipment::STATUS_DELIVERED)->count();
        $totalDelayed = (clone $baseQuery)->where('delay_hours', '>', 0)->count();

        $kpis = $this->etaService->calculateGlobalKpis();

        $openExceptions = ShipmentException::with('shipment')
            ->where('status', '!=', ShipmentException::STATUS_RESOLVED)
            ->when($user->isCustomer(), fn ($q) => $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $user->id)))
            ->orderByDesc('detected_at')
            ->take(5)
            ->get();

        return view('dashboard', [
            'shipments' => $shipments,
            'stats' => [
                'active' => $totalActive,
                'in_transit' => $totalInTransit,
                'delivered' => $totalDelivered,
                'delayed' => $totalDelayed,
            ],
            'kpis' => $kpis,
            'openExceptions' => $openExceptions,
            'carriersCount' => Carrier::count(),
            'locationsCount' => Location::count(),
        ]);
    }
}
