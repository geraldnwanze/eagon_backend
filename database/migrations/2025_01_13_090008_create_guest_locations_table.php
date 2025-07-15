<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guest_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_key');
            $table->foreign('tenant_key')->references('key')->on('tenants')->cascadeOnDelete();
            $table->foreignUuid('guest_id')->references('id')->on('guests')->constrained();
            $table->string('location_name')->nullable();
            $table->string('longitude');
            $table->string('latitude');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_locations');
    }
};
