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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_key');
            $table->foreign('tenant_key')->references('key')->on('tenants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->references('id')->on('users')->constrained();
            $table->string('full_name');
            $table->foreignUuid('estate_id')->references('id')->on('estates')->constrained();
            $table->string('phone_number');
            $table->date('valid_from_date')->nullable();
            $table->time('valid_from_time')->nullable();
            $table->date('valid_to_date')->nullable();
            $table->time('valid_to_time')->nullable();
            $table->string('invitation_code')->nullable()->unique();
            $table->string('invitation_status')->default('active');
            $table->string('email')->nullable();
            $table->string('premiss_status')->nullable();
            $table->timestamp('premiss_status_updated_at')->nullable();
            $table->string('entry_permission_status')->nullable();
            $table->timestamp('entry_permission_status_updated_at')->nullable();
            $table->string('entry_permission_reason')->nullable();
            $table->boolean('guest_accepted')->default(false);
            $table->string('fcm_token')->nullable();
            $table->string('device_id')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
