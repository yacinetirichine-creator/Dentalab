# Phase 0 — Cadrage

> **Trois semaines sans écrire une ligne de code métier.**
> C'est la phase la plus rentable du projet, et celle qu'on a le plus envie de sauter.

Le cahier des charges identifie le risque n° 1 du projet (§8) : *« Un outil logique sur
le papier mais inutilisable à l'établi. »* Ce n'est pas un risque théorique. La plupart
des logiciels de gestion métier échouent pour cette raison, pas pour des raisons
techniques.

Ce document explique **comment mener cette phase concrètement** : qui contacter, quoi
demander, quoi observer, quoi décider. Il complète le découpage en lots de
[`plan-de-travail.md`](plan-de-travail.md).

---

## Ce qu'il faut avoir à la fin

La phase 0 est finie quand ces cinq choses existent, pas quand trois semaines sont
passées.

| | Livrable | Où le ranger |
|---|---|---|
| 0.1 | 3 à 5 comptes rendus de visites de laboratoires | `docs/terrain/` |
| 0.2 | Les 7 questions ouvertes du cahier des charges (§9) ont toutes une réponse écrite | Section 9 mise à jour |
| 0.3 | La décision « nom du patient ou référence ? » est tranchée | `docs/decisions/001-donnees-patient.md` |
| 0.4 | Les 4 vérifications réglementaires sont faites par un professionnel | `docs/reglementaire/` |
| 0.5 | Les maquettes des 6 écrans clés sont validées par un labo | Outil de maquette, lien dans `docs/` |

---

## Semaine 1 — Trouver les laboratoires pilotes

### Combien

**Deux laboratoires pilotes, trois idéalement.** Un seul vous donnera ses habitudes
personnelles comme si c'était la norme du métier. Au-delà de cinq, vous n'apprendrez
plus rien de neuf et vous perdrez des semaines.

Visez des profils différents : un petit labo de 2–3 personnes, un labo de 10–15, si
possible un qui utilise déjà un logiciel et un qui travaille encore sur papier ou Excel.
Les deux vous apprendront des choses opposées et également utiles.

### Où les trouver

- **Votre réseau direct d'abord.** Un dentiste que vous connaissez travaille avec un
  labo. C'est l'introduction la plus facile et la plus efficace.
- **Les syndicats professionnels** (UNPPD, associations régionales de prothésistes) :
  ils publient des annuaires et organisent des rencontres.
- **Les salons dentaires.** Une journée sur place vaut vingt appels à froid.
- **Les fournisseurs de matériaux et d'équipement.** Leurs commerciaux connaissent tous
  les labos de leur région et font parfois les présentations.
- **LinkedIn et les pages jaunes**, en dernier recours : taux de réponse faible.

### Quoi leur proposer

Soyez direct sur ce que vous voulez et sur ce qu'ils y gagnent. Une trame qui marche :

> Bonjour,
>
> Je développe un logiciel de gestion pour laboratoires de prothèses dentaires
> (commandes, suivi de fabrication, traçabilité, facturation).
>
> Avant d'écrire la moindre ligne, je cherche à comprendre le métier depuis l'atelier.
> J'aimerais passer une demi-journée chez vous à observer comment un travail circule,
> de la réception à la livraison. Je ne vends rien et je ne prends pas de rendez-vous
> commercial : j'écoute et je note.
>
> En échange, vous seriez laboratoire pilote : accès gratuit au logiciel pendant sa
> première année, et vos remarques passent avant celles de tout le monde.
>
> Est-ce que ça vous intéresse ?

**Ce que vous offrez réellement** : la gratuité la première année, et le fait que
l'outil soit façonné sur leur manière de travailler. C'est un vrai avantage pour eux,
dites-le sans complexe.

**Ce que vous ne promettez pas** : une date de livraison, ni des fonctionnalités
précises. Vous n'en savez rien encore.

---

## Semaine 2 — La visite

### Le principe

**Vous observez, vous ne présentez pas.** Si vous arrivez avec des maquettes, ils
commenteront vos maquettes au lieu de vous raconter leur métier. Gardez les maquettes
pour la semaine 3.

Prévoyez une demi-journée minimum, le matin de préférence : c'est là que les coursiers
livrent et que la journée se planifie. Demandez l'autorisation de photographier les
fiches papier, les étiquettes, les tableaux muraux. **Une photo de leurs fiches papier
vaut une heure d'entretien** — elle vous montre exactement quelles informations comptent
pour eux.

### Le fil à suivre : un travail, du début à la fin

Demandez à suivre **un travail réel**, physiquement, depuis son arrivée. Notez chaque
manipulation, chaque papier, chaque fois que quelqu'un se déplace ou téléphone.

**À la réception**

- Comment arrive un travail ? Coursier, poste, dépôt par le dentiste, fichier numérique ?
- Combien par jour, en moyenne et en pointe ?
- Qui le saisit, et sur quoi ? Combien de temps prend la saisie ?
- Qu'est-ce qui est écrit sur le bon du dentiste, et qu'est-ce qui manque toujours ?
- Que se passe-t-il quand une information manque ? Qui rappelle le cabinet, et combien
  de fois par jour ?

**À l'atelier**

- Comment un technicien sait ce qu'il doit faire aujourd'hui ?
- Quelles sont les étapes réelles, dans l'ordre, pour une couronne ? Pour un stellite ?
  Pour une gouttière ? (Les étapes du cahier des charges sont une hypothèse à corriger.)
- Combien de personnes touchent un même travail ?
- Comment un travail passe d'un poste à l'autre : un bac, un chariot, un appel ?
- Où sont notés les matériaux et les numéros de lot ? Qui les note, et à quel moment ?
- **Regardez leurs mains.** Gants, plâtre, poussière, résine. Combien de fois peuvent-ils
  réalistement toucher un écran dans la journée ?

**À la livraison et au retour**

- Comment part un travail ? Coursier interne, prestataire, le dentiste passe le prendre ?
- Combien de travaux reviennent pour essayage ou retouche ? Quel pourcentage ?
- Comment savent-ils où en est un travail quand un dentiste appelle pour le demander ?
- Combien d'appels de dentistes par jour juste pour « où en est mon travail ? »

**À la facturation**

- Facture par travail, ou relevé mensuel ? Les deux selon le client ?
- Comment sont gérées les remises par cabinet ?
- Quelles erreurs de facturation arrivent, et à quelle fréquence ?
- Combien de temps passe le gérant sur la facturation chaque mois ?
- Comment relancent-ils les impayés ?

**Sur l'outil actuel**

- Qu'utilisent-ils, et depuis combien de temps ?
- Combien ça coûte par mois ?
- **Qu'est-ce qui les agace ? Notez leurs mots exacts.** C'est votre argumentaire commercial.
- Qu'est-ce qui les empêcherait de changer d'outil ? (Souvent : la peur de perdre
  l'historique. D'où l'importance de l'import de données.)
- Qui décide d'acheter un logiciel, ici ?

### Les trois questions qui font mal

Posez-les, même si elles sont inconfortables. Une réponse molle vaut un signal d'alarme.

1. *« Si je vous proposais cet outil à 120 € par mois, vous le prendriez ? »*
   Un « ça dépend » poli veut dire non.
2. *« Qu'est-ce qui vous a fait renoncer à changer de logiciel, la dernière fois ? »*
3. *« Qu'est-ce qui vous ferait arrêter de l'utiliser au bout de deux semaines ? »*

### Ce qu'il ne faut pas faire

- **Ne pas demander « de quoi auriez-vous besoin ? »** Les gens décrivent mal leur propre
  métier et vous répondront en listant des fonctionnalités de leur logiciel actuel.
  Demandez ce qu'ils *font*, pas ce qu'ils *voudraient*.
- **Ne pas corriger leur manière de travailler.** Vous êtes là pour comprendre, pas pour
  optimiser. Si quelque chose vous semble absurde, notez-le et cherchez pourquoi ça existe.
- **Ne pas promettre une fonctionnalité** pendant la visite.

### Le compte rendu

Écrivez-le **le jour même**, dans `docs/terrain/AAAA-MM-JJ-nom-du-labo.md`. Le lendemain
vous aurez déjà lissé les détails qui vous avaient surpris — et ce sont justement eux
qui comptent.

---

## Semaine 3 — Vérifier, décider, maquetter

### Les 4 vérifications réglementaires

Ces sujets conditionnent l'architecture. Se tromper coûte une refonte, pas une correction.
**Rien de ce qui est écrit au §4 du cahier des charges n'a été vérifié par un
professionnel** : ce sont des éléments de cadrage, pas un avis juridique.

**Avec un expert-comptable** (comptez 1 à 2 heures) :

- Quelles sont les règles de TVA actuelles sur les prothèses dentaires ? Quels cas
  d'exonération, et pour quels articles ?
- Où en est le calendrier de la facturation électronique pour une PME comme celle-ci ?
  Quelles obligations à quelle date ?
- Quelles mentions légales obligatoires sur une facture de laboratoire ?
- Durée d'archivage des factures, et sous quelle forme ?

**Avec un juriste santé ou un consultant qualité** (comptez 2 à 3 heures) :

- Que doit contenir exactement une déclaration de conformité pour un dispositif médical
  sur mesure (règlement UE 2017/745) ?
- Combien de temps le laboratoire doit-il conserver les données de traçabilité ?
- Si le logiciel stocke le nom d'un patient, l'hébergement certifié HDS devient-il
  obligatoire ? Et si on ne stocke qu'une référence ?
- Quel contrat de sous-traitance RGPD entre l'éditeur et chaque laboratoire ?

**Budget** : 2 000 à 5 000 € selon le cahier des charges (§7). C'est la dépense la plus
rentable du projet.

### La décision qui commande tout : nom du patient ou référence ?

| | Référence seule (ex. « DUP-2026-041 ») | Nom du patient |
|---|---|---|
| Hébergement | Standard en France, 50–150 €/mois | Probablement HDS, 300–800 €/mois |
| Responsabilité | Limitée | Données de santé, responsabilité lourde |
| Confort du labo | Le dentiste doit gérer la correspondance | Ils retrouvent un cas tout de suite |
| Réversibilité | Ajouter le nom plus tard = migration + HDS | Retirer le nom plus tard = facile |

Demandez aux labos visités ce qu'ils font aujourd'hui. S'ils écrivent le nom du patient
sur leurs fiches papier, ils voudront le faire dans le logiciel — il faudra soit les
convaincre, soit payer le HDS.

**Écrivez la décision dans `docs/decisions/001-donnees-patient.md`**, avec le raisonnement.
Vous la relirez dans six mois.

### Les concurrents

Une demi-journée suffit pour remplir cette grille. Logidents est la référence citée au
cahier des charges, mais il n'est pas seul.

| Concurrent | Prix affiché | Ce qu'il fait bien | Ce qui manque | Source |
|---|---|---|---|---|
| Logidents | | | | |
| | | | | |

Demandez aux labos visités : *« vous avez regardé quoi d'autre, avant ? »* C'est la
source la plus fiable.

### Les maquettes

Six écrans, pas plus. Dessinés à la main sur papier suffit — l'important est de les
montrer à un prothésiste et de le regarder essayer de s'en servir.

1. **Saisie d'une commande** — l'écran de la secrétaire, le plus dense
2. **Écran de scan à l'atelier** — le plus critique : *si le scan prend plus de trois
   secondes, il ne sera pas fait*
3. **Liste des travaux du jour** — ce que voit un technicien en arrivant
4. **Fiche d'un travail** — tout l'historique d'un travail sur un écran
5. **Facturation** — l'écran du gérant en fin de mois
6. **Extranet dentiste** — la vue du client

Testez l'écran de scan **en atelier, avec des gants**, pas sur votre bureau.

---

## Comment savoir si la phase 0 a réussi

Trois signes :

- Vous pouvez décrire le trajet d'une couronne dans un laboratoire, étape par étape, sans
  regarder vos notes.
- Au moins un gérant vous a dit une phrase que vous n'auriez jamais devinée tout seul.
- Vous avez supprimé ou modifié au moins une fonctionnalité du cahier des charges parce
  que le terrain l'a contredite.

**Si vous ressortez de la phase 0 avec un cahier des charges inchangé, c'est que vous
n'avez pas assez écouté.**
