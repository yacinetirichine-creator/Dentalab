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

## Conventions

Les règles du projet sont dans [`CLAUDE.md`](../../CLAUDE.md) à la racine.
[`AGENTS.md`](AGENTS.md) contient les conventions Laravel livrées avec le framework.
