# Dentalab

SaaS de gestion pour **laboratoires de prothèses dentaires** : commandes des dentistes,
fabrication, traçabilité réglementaire, livraison et facturation — dans un seul outil web,
sans installation, sur ordinateur, tablette et téléphone.

> Un laboratoire reçoit chaque jour des dizaines de travaux de plusieurs cabinets.
> Beaucoup travaillent encore avec des fiches papier, Excel ou un logiciel installé sur
> un seul PC. Dentalab centralise le cycle complet :
> **commande → fabrication → contrôle → livraison → facture → paiement.**

## État du projet

🟡 **Phase 0 — cadrage.** Le cahier des charges est écrit, le code n'a pas commencé.
Voir [`docs/plan-de-travail.md`](docs/plan-de-travail.md) pour la suite.

## Documents de référence

| Document | Rôle |
|---|---|
| [`docs/cahier-des-charges.md`](docs/cahier-des-charges.md) | **Référence** : périmètre, réglementaire, technique, planning, budget, risques |
| [`docs/plan-de-travail.md`](docs/plan-de-travail.md) | Découpage en lots, phase par phase |
| [`docs/modele-de-donnees.md`](docs/modele-de-donnees.md) | Première esquisse des tables, à valider avant la première migration |

## Structure du dépôt

```
dentalab/
├── apps/
│   └── web/          Application Laravel + Inertia + React (cœur du produit)
├── packages/         (à venir) Design system et types partagés
└── docs/             Cahier des charges, plan de travail, modèle de données
```

## Stack

- **Backend** : Laravel (PHP 8.4), PostgreSQL, Redis (files d'attente)
- **Frontend** : Inertia.js + React + TypeScript
- **Documents** : génération PDF (bons de travail, bons de livraison, factures, fiches de traçabilité)
- **Codes-barres** : QR codes générés côté serveur, lus par la caméra du téléphone ou une douchette USB
- **Hébergement** : France (OVHcloud, Scaleway ou équivalent) ; certifié HDS si des noms de patients sont stockés

C'est la même stack que le projet Jarvis, pour réutiliser les briques déjà écrites
(authentification, rôles, PDF, facturation).

## Démarrer en local

```bash
cd apps/web
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

Tests et formatage :

```bash
cd apps/web
php artisan test        # tests backend
vendor/bin/pint         # formatage PSR-12
npm run types           # vérification des types TypeScript
npm run build           # construction des assets
```

Ces quatre commandes sont exactement ce que lance la CI à chaque pull request
(voir [`.github/workflows/ci.yml`](.github/workflows/ci.yml)).

## Règles de développement (non négociables)

1. **Cloisonnement** : toute table métier porte `laboratoire_id` ; chaque module est livré
   avec son test « le labo A ne voit pas les données du labo B ».
2. **Aucune concaténation SQL** : Eloquent ou query builder uniquement.
3. **Chargement anticipé systématique** : une requête N+1 est un bug, pas une optimisation.
4. **Calculs monétaires testés** : remises, TVA, avoirs, relevés — tous couverts par des tests.
5. **Factures immuables** : une facture émise ne se modifie pas, on émet un avoir.
   La numérotation est continue, sans trou.
6. **Données de santé** : référence patient pseudonymisée par défaut, journal des accès,
   aucune donnée patient en clair dans les journaux techniques.
7. **Internationalisation** : aucune chaîne d'interface en dur, même en français seul.

## Réglementaire — à valider avant le développement

Quatre sujets conditionnent la conception et **doivent être validés par un juriste ou un
expert-comptable** (détail en §4 du cahier des charges) :

- Dispositifs médicaux sur mesure (règlement européen 2017/745)
- Facturation électronique (Factur-X, plateforme agréée)
- TVA sur les prothèses
- RGPD et hébergement HDS des données de santé
