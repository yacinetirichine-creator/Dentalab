<?php

namespace Database\Factories;

use App\Models\Cabinet;
use App\Models\Laboratoire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cabinet>
 */
class CabinetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'laboratoire_id' => Laboratoire::factory(),
            'raison_sociale' => 'Cabinet '.fake()->lastName(),
            'siret' => fake()->numerify('##############'),
            'email' => fake()->unique()->companyEmail(),
            'telephone' => fake()->numerify('0#########'),
            'adresse_ligne_1' => fake()->streetAddress(),
            'code_postal' => fake()->numerify('#####'),
            'ville' => fake()->city(),
            'pays' => 'FR',
            'delai_reglement_jours' => 30,
        ];
    }

    public function archive(): static
    {
        return $this->state(fn (array $attributs) => ['archive_le' => now()]);
    }
}
