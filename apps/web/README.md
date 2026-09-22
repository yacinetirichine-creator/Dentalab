# apps/web — application Dentalab

Application Laravel + Inertia + React. C'est le cœur du produit.

Le périmètre et l'ordre des lots sont décrits à la racine du dépôt, dans
[`docs/cahier-des-charges.md`](../../docs/cahier-des-charges.md) et
[`docs/plan-de-travail.md`](../../docs/plan-de-travail.md).

## Démarrer

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm install
npm run dev          # dans un terminal
php artisan serve    # dans un autre
```

Par défaut la base est un fichier SQLite (`database/database.sqlite`), suffisant pour
développer. La production tournera sur PostgreSQL.

## Vérifier avant de pousser

```bash
php artisan test        # tests backend
vendor/bin/pint         # formatage PSR-12
npm run types           # types TypeScript
npm run build           # construction des assets
```

Ce sont exactement les quatre commandes de la CI.

## Organisation du front

```
resources/js/
├── app.tsx           Point d'entrée Inertia
└── pages/            Une page = un composant React, résolu par son nom
    └── Accueil.tsx
```

Côté serveur, une route renvoie `Inertia::render('NomDeLaPage', [...props])`.

## Le cloisonnement entre laboratoires

C'est la règle n° 1 du projet. Chaque table métier appartient à un laboratoire, et
aucun laboratoire ne doit jamais voir les données d'un autre.

**Pour toute nouvelle table métier**, deux choses à faire — et rien d'autre :

```php
// 1. Dans la migration
$table->foreignId('laboratoire_id')->constrained();

// 2. Dans le modèle
use App\Models\Concerns\AppartientAuLaboratoire;

class Travail extends Model
{
    use AppartientAuLaboratoire;
}
```

À partir de là, tout est automatique :

- Chaque requête est filtrée sur le laboratoire courant. `Travail::all()` ne renvoie
  jamais le travail d'un autre labo, et `Travail::find($id)` renvoie `null` si l'objet
  appartient à quelqu'un d'autre.
- `laboratoire_id` est rempli tout seul à la création.
- Sans laboratoire courant défini, une exception est levée — le code refuse de servir
  des données non cloisonnées plutôt que de tout renvoyer.

Le laboratoire courant vient du **compte connecté**, jamais de l'URL ni d'un champ de
formulaire (middleware `DefinirLaboratoireCourant`). Un utilisateur ne peut donc pas
désigner le laboratoire d'un concurrent.

Pour les rares cas où l'accès global est légitime (commande artisan, administration de
la plateforme), il doit être explicite :

```php
app(LaboratoireCourant::class)->sansCloisonnement(fn () => Travail::all());
```

Les garde-fous sont testés dans `tests/Feature/CloisonnementTest.php`.

## Conventions

Les règles du projet sont dans [`CLAUDE.md`](../../CLAUDE.md) à la racine.
[`AGENTS.md`](AGENTS.md) contient les conventions Laravel livrées avec le framework.
