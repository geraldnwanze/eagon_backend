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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_key');
            $table->foreign('tenant_key')->references('key')->on('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->references('id')->on('users')->constrained();
            $table->boolean( 'allow_all_notifications')->default(false);
            $table->boolean('allow_message_notifications')->default(false);
            $table->boolean( 'allow_work_notifications')->default(false);
            $table->boolean( 'allow_location_update_notification')->default(false);
            $table->string('start_work_notification_time_before')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
