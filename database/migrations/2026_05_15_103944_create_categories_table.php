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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->string('icon')->nullable(); // Lucide icon name or emoji
            $table->string('color')->default('#4F46E5');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Seed default categories
        $defaults = [
            ['name' => 'طعام وشراب', 'type' => 'expense', 'icon' => '🍔', 'color' => '#F59E0B', 'is_default' => true],
            ['name' => 'مواصلات', 'type' => 'expense', 'icon' => '🚗', 'color' => '#3B82F6', 'is_default' => true],
            ['name' => 'إيجار سكن', 'type' => 'expense', 'icon' => '🏠', 'color' => '#EF4444', 'is_default' => true],
            ['name' => 'فواتير', 'type' => 'expense', 'icon' => '⚡', 'color' => '#F43F5E', 'is_default' => true],
            ['name' => 'تسوق', 'type' => 'expense', 'icon' => '🛍️', 'color' => '#8B5CF6', 'is_default' => true],
            ['name' => 'صحة', 'type' => 'expense', 'icon' => '💊', 'color' => '#10B981', 'is_default' => true],
            ['name' => 'ترفيه', 'type' => 'expense', 'icon' => '🎬', 'color' => '#EC4899', 'is_default' => true],
            ['name' => 'راتب شهري', 'type' => 'income', 'icon' => '💰', 'color' => '#10B981', 'is_default' => true],
            ['name' => 'عمل حر', 'type' => 'income', 'icon' => '💻', 'color' => '#6366F1', 'is_default' => true],
            ['name' => 'استثمارات', 'type' => 'income', 'icon' => '📈', 'color' => '#F59E0B', 'is_default' => true],
            ['name' => 'هدايا', 'type' => 'income', 'icon' => '🎁', 'color' => '#D946EF', 'is_default' => true],
        ];

        foreach ($defaults as $item) {
            \DB::table('categories')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
