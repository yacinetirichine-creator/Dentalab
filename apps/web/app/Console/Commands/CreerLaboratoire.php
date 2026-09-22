<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\Laboratoire;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Crée un laboratoire et son compte gérant.
 *
 * Il n'y a pas d'inscription publique : un laboratoire est mis en service par
 * l'administrateur de la plateforme, qui transmet ensuite ses identifiants au
 * gérant. Le mot de passe est généré ici et affiché une seule fois.
 */
class CreerLaboratoire extends Command
{
    protected $signature = 'laboratoire:creer
                            {--nom= : Raison sociale du laboratoire}
                            {--gerant= : Nom du gérant}
                            {--email= : Adresse e-mail du gérant}';

    protected $description = 'Crée un laboratoire et le compte de son gérant';

    public function handle(): int
    {
        $nom = $this->option('nom') ?: $this->ask('Raison sociale du laboratoire');
        $gerant = $this->option('gerant') ?: $this->ask('Nom du gérant');
        $email = $this->option('email') ?: $this->ask('Adresse e-mail du gérant');

        if (User::where('email', $email)->exists()) {
            $this->error("L'adresse {$email} est déjà utilisée.");

            return self::FAILURE;
        }

        $motDePasse = Str::password(16);

        DB::transaction(function () use ($nom, $gerant, $email, $motDePasse) {
            $laboratoire = Laboratoire::create(['nom' => $nom]);

            User::create([
                'laboratoire_id' => $laboratoire->id,
                'name' => $gerant,
                'email' => $email,
                'password' => $motDePasse,
                'role' => Role::Gerant,
            ]);
        });

        $this->info("Laboratoire « {$nom} » créé.");
        $this->newLine();
        $this->line("  Identifiant : {$email}");
        $this->line("  Mot de passe : {$motDePasse}");
        $this->newLine();
        $this->comment('Ce mot de passe ne sera plus affiché. Transmettez-le au gérant');
        $this->comment('par un canal sûr. La double authentification lui sera demandée');
        $this->comment('à sa première connexion.');

        return self::SUCCESS;
    }
}
