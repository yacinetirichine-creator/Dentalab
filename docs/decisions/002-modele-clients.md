# 002 — Modèle des clients : ce qui est supposé

- **Date** : 2026-09-22
- **Statut** : provisoire — **à confirmer en phase 0**

## La question

Le lot 1.4 (cabinets, praticiens, adresses de livraison) a été construit **avant**
les visites de laboratoires. Sa forme repose donc sur le cahier des charges et sur
des conventions du métier, pas sur l'observation.

Ce document liste chaque hypothèse, pour que la première visite serve à les
confirmer ou à les corriger — et pour que la correction coûte peu.

## Les hypothèses, une par une

| # | Hypothèse retenue | Ce qui la remettrait en cause | Coût de la correction |
|---|---|---|---|
| 1 | **Le cabinet est le client facturé**, le praticien est celui qui commande. | Des labos facturent le praticien directement, pas la structure. | Moyen : la facturation (lot 3.4) pointerait vers le praticien. À trancher **avant** le lot 3. |
| 2 | **Un praticien appartient à un seul cabinet.** | Un dentiste exerçant dans deux cabinets. | Élevé : il faudrait une table de liaison. **La question la plus importante à poser.** |
| 3 | **Un cabinet a plusieurs adresses de livraison**, dont une principale. | Un labo qui ne livre jamais qu'à une adresse : la table est alors inutilement lourde. | Faible : on masque l'écran. |
| 4 | **L'adresse de facturation est portée par le cabinet**, séparée des adresses de livraison. | Confusion à la saisie si elles sont toujours identiques. | Faible : pré-remplissage. |
| 5 | **Délai de règlement en jours, par cabinet**, 30 par défaut. | Des conditions plus riches (fin de mois, 45 jours fin de mois, escompte). | Moyen : changer le type du champ. |
| 6 | **Deux cabinets d'un même laboratoire ne partagent pas un SIRET.** Deux laboratoires peuvent avoir le même cabinet pour client. | Un cabinet à plusieurs établissements, chacun son SIRET — l'hypothèse tient. Un labo saisissant deux fiches pour le même cabinet volontairement — elle ne tient plus. | Faible : retirer la règle. |
| 7 | **On archive, on ne supprime jamais.** | Aucune : la conservation des factures et de la traçabilité l'impose (§4 du cahier des charges). | — Cette hypothèse n'est pas négociable. |
| 8 | **Seul le gérant gère les clients.** La secrétaire saisit les commandes mais ne crée pas de cabinet. | Dans beaucoup de labos, c'est la secrétaire qui crée les fiches clients. | Très faible : une ligne dans `App\Enums\Role`. **Probablement à corriger.** |
| 9 | **Le numéro RPPS fait onze chiffres et reste facultatif.** | Des labos ne le notent jamais, ou notent l'ADELI. | Faible. |

## Ce que ça implique

Le code est écrit pour que ces hypothèses soient **localisées** :

- Les champs vivent dans les migrations et les `FormRequest`, pas éparpillés.
- Les droits vivent dans `App\Enums\Role`, en un seul endroit.
- Rien dans le reste de l'application ne dépend encore de ces tables : les lots
  suivants (commandes, facturation) ne sont pas écrits.

**C'est le bon moment pour se tromper.** Après le lot 2 (commandes), une
correction du modèle client coûtera nettement plus cher.

## À faire

Reprendre ce tableau pendant la première visite de laboratoire, question par
question, et mettre à jour le statut de ce document.
