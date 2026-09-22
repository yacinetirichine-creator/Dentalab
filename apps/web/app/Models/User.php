<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'laboratoire_id', 'role'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Le laboratoire auquel l'utilisateur appartient.
     *
     * Vaut null pour l'administrateur de la plateforme, qui n'appartient à
     * aucun laboratoire (§2 du cahier des charges).
     *
     * @return BelongsTo<Laboratoire, $this>
     */
    public function laboratoire(): BelongsTo
    {
        return $this->belongsTo(Laboratoire::class);
    }

    /**
     * L'utilisateur a-t-il le droit de faire ceci ?
     *
     * Un utilisateur sans rôle n'a aucun droit : l'absence de rôle est un
     * refus, jamais une autorisation.
     */
    public function peut(Permission $permission): bool
    {
        return in_array($permission, $this->role?->permissions() ?? [], strict: true);
    }

    /**
     * Les permissions effectives de l'utilisateur.
     *
     * @return list<Permission>
     */
    public function permissions(): array
    {
        return $this->role?->permissions() ?? [];
    }

    /**
     * La double authentification est-elle exigée de cet utilisateur sans
     * qu'il l'ait encore activée ?
     */
    public function doitActiverLaDoubleAuthentification(): bool
    {
        return (bool) $this->role?->doubleAuthentificationObligatoire()
            && $this->two_factor_confirmed_at === null;
    }
}
