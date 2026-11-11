<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dateTime('expected_delivery_at')->nullable()->after('departure_at');
            $table->boolean('is_delayed')->default(false)->index()->after('delay_hours');
            $table->string('delay_reason', 500)->nullable()->after('is_delayed');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['expected_delivery_at', 'is_delayed', 'delay_reason']);
        });
    }
};
