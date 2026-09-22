<?php

namespace Database\Factories;

use App\Models\AdresseLivraison;
use App\Models\Cabinet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdresseLivraison>
 */
class AdresseLivraisonFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cabinet_id' => Cabinet::factory(),
            'laboratoire_id' => fn (array $attributs) => Cabinet::withoutGlobalScope('laboratoire')
                ->whereKey($attributs['cabinet_id'])
                ->value('laboratoire_id'),
            'libelle' => 'Cabinet principal',
            'adresse_ligne_1' => fake()->streetAddress(),
            'code_postal' => fake()->numerify('#####'),
            'ville' => fake()->city(),
            'pays' => 'FR',
            'est_principale' => true,
        ];
    }

    public function archive(): static
    {
        return $this->state(fn (array $attributs) => ['archive_le' => now()]);
    }
}
