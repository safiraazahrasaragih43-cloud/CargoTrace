<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationWebController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Display paginated Ports & Locations directory.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Location::class);

        $locations = $this->locationService->getLocations(
            $request->only(['search', 'type']),
            12
        );

        return view('locations.index', [
            'locations' => $locations,
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    /**
     * Store a new port/hub location via web modal.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $location = $this->locationService->createLocation($request->validated());

        return redirect()
            ->route('locations.index')
            ->with('status', "Lokasi {$location->name} ({$location->code}) berhasil ditambahkan.");
    }

    /**
     * Delete a location (Admin only).
     */
    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('delete', $location);

        $name = $location->name;
        $this->locationService->deleteLocation($location);

        return redirect()
            ->route('locations.index')
            ->with('status', "Lokasi {$name} berhasil dihapus.");
    }
}

