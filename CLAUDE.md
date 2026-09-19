# Instructions pour Claude Code

## Le projet

Dentalab est un SaaS de gestion pour laboratoires de prothèses dentaires.
Avant toute tâche, lire [`docs/cahier-des-charges.md`](docs/cahier-des-charges.md)
(le quoi) et [`docs/plan-de-travail.md`](docs/plan-de-travail.md) (l'ordre des lots).

Le propriétaire du projet est **novice en développement**. Expliquer les choix
techniques en français simple, sans jargon inutile, et signaler ce qui engage
durablement le projet (choix de base de données, format des factures, stockage des
données patient).

Les conventions Laravel livrées avec le framework sont dans
[`apps/web/AGENTS.md`](apps/web/AGENTS.md). Les règles ci-dessous les complètent et,
en cas de contradiction, priment.

## Langue

Tout est en français : code métier, noms de tables et de colonnes, commentaires,
messages de commit, documentation, interface. Les mots-clés du framework restent
en anglais, évidemment.

## Méthode de travail

- **Un lot = une branche = une pull request.** Les lots sont décrits dans
  `docs/plan-de-travail.md`. Ne pas en commencer un nouveau avant que le précédent
  soit fusionné.
- **Les tests d'abord** sur tout ce qui touche à l'argent (remises, TVA, avoirs,
  relevés) et au cloisonnement entre laboratoires.
- **Ne pas élargir le périmètre.** Le risque « dispersion » est identifié au §8 du
  cahier des charges. Si une bonne idée surgit, l'ajouter à la V2 dans le plan de
  travail, pas au lot en cours.

## Règles non négociables

1. Toute table métier porte `laboratoire_id`, et chaque module est livré avec son
   test de cloisonnement.
2. Aucune concaténation SQL : Eloquent ou query builder uniquement.
3. Chargement anticipé systématique : une requête N+1 est un bug.
4. Une facture émise est immuable ; la correction passe par un avoir. La
   numérotation est continue, sans trou, générée en base sous verrou.
5. Référence patient pseudonymisée par défaut. Aucune donnée patient dans les
   journaux techniques. La question de l'hébergement HDS est tranchée en phase 0.
6. Aucune chaîne d'interface en dur, même en français seul.

## Points sensibles du métier

- **Les allers-retours d'essayage sont la norme, pas l'exception** : un travail part
  chez le dentiste, revient à l'atelier, repart. Le modèle de données le prévoit dès
  le départ.
- **Le scan à l'atelier doit valider une étape en moins d'une seconde.** Au-delà de
  trois secondes, les prothésistes ne le feront pas. L'écran de scan se teste avec
  des gants.
- **Une erreur de facturation fait perdre la confiance du gérant immédiatement.**

## Ce qui n'est pas encore tranché

Les questions ouvertes sont listées au §9 du cahier des charges. Tant qu'une question
n'a pas de réponse écrite, ne pas la trancher seul dans le code : la poser.
