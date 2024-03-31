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
        Schema::create('shopify_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('session_id')->nullable(false)->unique();
            $table->string('shop')->nullable(false);
            $table->string('shop_id')->nullable();
            $table->string('state')->nullable(false);
            $table->boolean('is_online')->nullable(false);
            $table->string('scope')->nullable();
            $table->string('access_token')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('user_full_name')->nullable();
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_country_code')->nullable();
            $table->string('user_province')->nullable();
            $table->string('myshopify_domain')->nullable();
            $table->boolean('user_email_verified')->nullable();
            $table->boolean('account_owner')->nullable();
            $table->string('locale')->nullable();
            $table->boolean('collaborator')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_sessions');
    }
};
