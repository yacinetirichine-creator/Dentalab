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

## Comptes, rôles et droits

Six profils (§2 du cahier des charges), définis dans `app/Enums/Role.php`. Chaque rôle
porte une liste de permissions (`app/Enums/Permission.php`) ; les permissions ne sont
jamais attribuées à une personne en particulier.

| Rôle | Ce qu'il peut faire |
|---|---|
| Gérant | Paramétrer le labo, clients, catalogue, facturer, indicateurs, saisir une commande, valider une étape |
| Prothésiste | Valider une étape de fabrication |
| Secrétaire | Saisir une commande, préparer une livraison |
| Livreur | Consulter sa tournée |
| Dentiste | Commander en ligne, suivre ses travaux |
| Administrateur de la plateforme | Administrer la plateforme — et rien d'autre : il n'accède à aucune donnée métier |

**Un compte sans rôle n'a aucun droit.** Le refus est la valeur par défaut.

Chaque permission devient une capacité du framework, utilisable partout :

```php
Route::get('/facturation', ...)->middleware('can:facturer');   // dans les routes
$utilisateur->can('facturer');                                  // dans le code
$utilisateur->peut(Permission::Facturer);                       // forme typée
```

Le menu affiché est déduit des permissions (`app/Support/Navigation/Menu.php`), mais
**cacher une entrée n'est pas une sécurité** : chaque route porte aussi sa propre
vérification.

### Connexion et double authentification

L'authentification est assurée par [Laravel Fortify](https://laravel.com/docs/fortify),
l'implémentation officielle — le code de la double authentification (TOTP, codes de
secours) n'est pas écrit à la main. Les écrans sont des pages Inertia normales,
branchées dans `app/Providers/FortifyServiceProvider.php`.

**La double authentification est obligatoire pour le gérant et l'administrateur de la
plateforme.** Tant qu'ils ne l'ont pas activée, le middleware
`ExigerDoubleAuthentification` les renvoie vers l'écran d'activation. Les autres rôles
peuvent l'activer sans y être contraints.

Il n'y a pas d'inscription publique : un laboratoire est mis en service par la commande

```bash
php artisan laboratoire:creer
```

qui crée le laboratoire, son compte gérant et un mot de passe affiché une seule fois.

## Clients : cabinets, praticiens, adresses

Trois tables, toutes cloisonnées par laboratoire :

- **`cabinets`** — le client au sens commercial, celui qu'on facture. Porte
  l'adresse de facturation et le délai de règlement.
- **`praticiens`** — les dentistes rattachés à un cabinet. Ce sont eux qui commandent.
- **`adresses_livraison`** — où porter les travaux. Un cabinet peut en avoir
  plusieurs, dont une principale.

**Rien ne se supprime, tout s'archive** (trait `Archivable`). Un cabinet porte un
historique de travaux et de factures qui doit être conservé plusieurs années (§4 du
cahier des charges). `archiver()` le retire des listes, `restaurer()` l'y remet.

```php
Cabinet::actifs()->get();     // ce qu'on affiche par défaut
Cabinet::archives()->get();   // l'onglet « archivés »
```

> ⚠️ **Ce modèle repose sur des hypothèses non vérifiées.** Il a été construit avant
> les visites de laboratoires. Les neuf hypothèses et le coût de leur correction sont
> listés dans [`docs/decisions/002-modele-clients.md`](../../docs/decisions/002-modele-clients.md).
> La plus lourde : *un praticien appartient-il à un seul cabinet ?*

## Les textes affichés

Aucune chaîne d'interface n'est écrite dans un composant. Tout vit dans
`lang/fr/` :

| Fichier | Contenu |
|---|---|
| `interface.php` | Les textes des écrans, du menu et des titres |
| `roles.php`, `permissions.php` | Les libellés des rôles et des droits |
| `validation.php`, `auth.php`, `passwords.php`, `pagination.php` | Les messages de Laravel, en français |

Côté serveur, `__('interface.connexion.email')` comme d'habitude. Côté React, les
traductions arrivent par Inertia et se lisent avec un crochet :

```tsx
const t = useTraduction();

<button>{t('connexion.seConnecter')}</button>
<p>{t('chantier.explication', { lot: '2.1' })}</p>
```

Une clé absente s'affiche telle quelle à l'écran — mieux vaut voir
`connexion.email` qu'un blanc silencieux.

**Trois tests tiennent la règle** (`tests/Feature/InterfaceTraduiteTest.php`) :

- chaque composant est relu, et le test échoue s'il contient du texte entre
  deux balises, un attribut visible littéral, ou un caractère accentué hors
  commentaire ;
- toute clé appelée par un composant doit exister dans `interface.php` ;
- le garde-fou lui-même est testé sur deux pièges, pour qu'il ne devienne pas
  un test qui ne vérifie plus rien.

Pour ajouter une langue : dupliquer `lang/fr/` dans `lang/<langue>/` et poser
`APP_LOCALE`.

## Conventions

Les règles du projet sont dans [`CLAUDE.md`](../../CLAUDE.md) à la racine.
[`AGENTS.md`](AGENTS.md) contient les conventions Laravel livrées avec le framework.
