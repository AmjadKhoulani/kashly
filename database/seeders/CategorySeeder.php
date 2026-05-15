<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Expenses
            ['name' => 'طعام وشراب', 'type' => 'expense', 'icon' => '🍔', 'color' => '#F59E0B'],
            ['name' => 'مواصلات', 'type' => 'expense', 'icon' => '🚗', 'color' => '#3B82F6'],
            ['name' => 'إيجار سكن', 'type' => 'expense', 'icon' => '🏠', 'color' => '#EF4444'],
            ['name' => 'فواتير', 'type' => 'expense', 'icon' => '⚡', 'color' => '#F43F5E'],
            ['name' => 'تسوق', 'type' => 'expense', 'icon' => '🛍️', 'color' => '#8B5CF6'],
            ['name' => 'صحة', 'type' => 'expense', 'icon' => '💊', 'color' => '#10B981'],
            ['name' => 'ترفيه', 'type' => 'expense', 'icon' => '🎬', 'color' => '#EC4899'],
            
            // Income
            ['name' => 'راتب شهري', 'type' => 'income', 'icon' => '💰', 'color' => '#10B981'],
            ['name' => 'عمل حر', 'type' => 'income', 'icon' => '💻', 'color' => '#6366F1'],
            ['name' => 'استثمارات', 'type' => 'income', 'icon' => '📈', 'color' => '#F59E0B'],
            ['name' => 'هدايا', 'type' => 'income', 'icon' => '🎁', 'color' => '#D946EF'],
        ];

        foreach ($defaults as $item) {
            Category::create(array_merge($item, ['is_default' => true]));
        }
    }
}
