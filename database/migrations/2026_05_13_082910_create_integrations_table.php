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
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // shopify, whmcs, etc.
            $table->string('name'); // e.g. "My Shopify Store"
            $table->json('settings')->nullable(); // API keys, secrets, etc.
            $table->string('webhook_secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->morphs('target'); // Where to deposit the income (Wallet or Business)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
