<?php

namespace Database\Factories;

use App\Models\Laboratoire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Laboratoire>
 */
class LaboratoireFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => 'Laboratoire '.fake()->lastName(),
            'siret' => fake()->numerify('##############'),
            'ville' => fake()->city(),
            'prefixe_numerotation' => Str::upper(fake()->unique()->lexify('???')),
            'actif' => true,
        ];
    }

    public function inactif(): static
    {
        return $this->state(fn (array $attributs) => ['actif' => false]);
    }
}
