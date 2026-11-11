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
use Illuminate\Support\Facades\DB;
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

        $shipments = $this->shipmentService->getShipments($user, $request->only(['search', 'status']), 10);

        $baseQuery = Shipment::query();
        if ($user->isCustomer()) {
            $baseQuery->where('customer_id', $user->id);
        }

        $totalShipments = (clone $baseQuery)->count();
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

        // Live database Chart.js aggregations
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $carrierCounts = (clone $baseQuery)
            ->join('carriers', 'shipments.carrier_id', '=', 'carriers.id')
            ->select('carriers.name as carrier_name', DB::raw('count(*) as total'))
            ->groupBy('carriers.name')
            ->pluck('total', 'carrier_name')
            ->toArray();

        $exceptionTypeCounts = ShipmentException::query()
            ->when($user->isCustomer(), fn ($q) => $q->whereHas('shipment', fn ($sq) => $sq->where('customer_id', $user->id)))
            ->select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        return view('dashboard', [
            'shipments' => $shipments,
            'stats' => [
                'total' => $totalShipments,
                'active' => $totalActive,
                'in_transit' => $totalInTransit,
                'delivered' => $totalDelivered,
                'delayed' => $totalDelayed,
                'on_time' => max(0, $totalShipments - $totalDelayed),
            ],
            'kpis' => $kpis,
            'openExceptions' => $openExceptions,
            'charts' => [
                'status' => $statusCounts,
                'carriers' => $carrierCounts,
                'exceptions' => $exceptionTypeCounts,
            ],
            'carriersCount' => Carrier::count(),
            'locationsCount' => Location::count(),
        ]);
    }
}
