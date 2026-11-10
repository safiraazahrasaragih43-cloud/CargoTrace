<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Models\Carrier;
use App\Services\CarrierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrierWebController extends Controller
{
    public function __construct(
        protected CarrierService $carrierService
    ) {}

    /**
     * Display paginated Carriers directory.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Carrier::class);

        $carriers = $this->carrierService->getCarriers(
            $request->only(['search', 'is_active']),
            12
        );

        return view('carriers.index', [
            'carriers' => $carriers,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    /**
     * Store a new carrier via web modal.
     */
    public function store(StoreCarrierRequest $request): RedirectResponse
    {
        $carrier = $this->carrierService->createCarrier($request->validated());

        return redirect()
            ->route('carriers.index')
            ->with('status', "Carrier {$carrier->name} ({$carrier->code}) added successfully.");
    }

    /**
     * Delete a carrier (Admin only).
     */
    public function destroy(Carrier $carrier): RedirectResponse
    {
        $this->authorize('delete', $carrier);

        $name = $carrier->name;
        $this->carrierService->deleteCarrier($carrier);

        return redirect()
            ->route('carriers.index')
            ->with('status', "Carrier {$name} removed.");
    }
}
