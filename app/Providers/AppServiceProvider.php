<?php

namespace App\Providers;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\TrackingEvent;
use App\Policies\CarrierPolicy;
use App\Policies\LocationPolicy;
use App\Policies\ShipmentExceptionPolicy;
use App\Policies\ShipmentPolicy;
use App\Policies\TrackingEventPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Carrier::class, CarrierPolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(Shipment::class, ShipmentPolicy::class);
        Gate::policy(ShipmentException::class, ShipmentExceptionPolicy::class);
        Gate::policy(TrackingEvent::class, TrackingEventPolicy::class);
    }
}
