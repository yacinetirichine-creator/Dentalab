<?php

namespace Database\Factories;

use App\Models\Cabinet;
use App\Models\Praticien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Praticien>
 */
class PraticienFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cabinet = Cabinet::factory();

        return [
            'cabinet_id' => $cabinet,
            // Le praticien appartient au même laboratoire que son cabinet.
            'laboratoire_id' => fn (array $attributs) => Cabinet::withoutGlobalScope('laboratoire')
                ->whereKey($attributs['cabinet_id'])
                ->value('laboratoire_id'),
            'civilite' => fake()->randomElement(['Dr', 'Dre']),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'numero_rpps' => fake()->numerify('###########'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    public function archive(): static
    {
        return $this->state(fn (array $attributs) => ['archive_le' => now()]);
    }
}
