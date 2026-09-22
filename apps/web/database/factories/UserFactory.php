<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Par défaut le rôle le moins doté, pour qu'un test qui oublie de
            // préciser le rôle ne donne pas accidentellement tous les droits.
            'role' => Role::Prothesiste,
        ];
    }

    /** Attribue un rôle précis. */
    public function role(Role $role): static
    {
        return $this->state(fn (array $attributs) => ['role' => $role]);
    }

    /** Un compte dont le rôle n'a pas encore été attribué : aucun droit. */
    public function sansRole(): static
    {
        return $this->state(fn (array $attributs) => ['role' => null]);
    }

    /** Un compte dont la double authentification est déjà activée. */
    public function avecDoubleAuthentification(): static
    {
        return $this->state(fn (array $attributs) => [
            'two_factor_secret' => encrypt('SECRETDETEST1234'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code-de-secours'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
