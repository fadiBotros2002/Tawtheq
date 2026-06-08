<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);
        $slug = Str::slug($nameEn);

        return [
            'user_id' => User::factory(),
            'name_ar' => 'تصنيف '.fake()->unique()->word(),
            'name_en' => ucwords($nameEn),
            'slug' => $slug,
        ];
    }
}
