# Plan de travail — découpage en lots

Le cahier des charges ([`cahier-des-charges.md`](cahier-des-charges.md)) décrit **quoi** construire.
Ce document décrit **dans quel ordre**, sous forme de lots livrables un par un.

Règle d'or : **un lot = une branche = une pull request = des tests qui passent.**
Aucun lot ne commence avant que le précédent soit fusionné.

---

## Phase 0 — Cadrage (3 semaines, avant tout code)

Cette phase ne produit pas de code. Elle produit des décisions, et ces décisions
conditionnent tout le reste.

| Lot | Livrable | Comment savoir que c'est fini |
|---|---|---|
| 0.1 | Visites de 3 à 5 laboratoires, compte rendu écrit par visite | Les comptes rendus sont dans `docs/terrain/` |
| 0.2 | Réponses aux 7 questions ouvertes du cahier des charges (§9) | La section 9 n'a plus de case à cocher vide |
| 0.3 | Décision HDS : stocke-t-on le nom du patient ? | La réponse est écrite dans `docs/decisions/001-donnees-patient.md` |
| 0.4 | Vérification réglementaire (TVA prothèses, facture électronique, MDR 2017/745) par un professionnel | Un compte rendu signé ou un e-mail du conseil dans `docs/reglementaire/` |
| 0.5 | Maquettes des 6 écrans clés : saisie de commande, écran de scan atelier, liste des travaux, fiche travail, facturation, extranet dentiste | Les maquettes sont validées par au moins un labo pilote |

> **Ne pas sauter cette phase.** Le risque n° 1 du projet (§8 du cahier des charges)
> est de construire un outil logique sur le papier mais inutilisable à l'établi.

**Comment la mener concrètement** — où trouver les laboratoires pilotes, quoi leur
proposer, la trame de visite question par question, les vérifications réglementaires à
demander à un expert-comptable et à un juriste : [`phase-0-cadrage.md`](phase-0-cadrage.md).

---

## Phase 1 — Socle (3 semaines)

| Lot | Contenu | Tests attendus |
|---|---|---|
| 1.1 ✅ | Squelette Laravel + Inertia + React, CI verte | Un test « la page d'accueil répond 200 » |
| 1.2 ✅ | Multi-tenant : table `laboratoires`, colonne `laboratoire_id` sur toute table métier, filtrage automatique | Test de cloisonnement : le labo A ne voit jamais une donnée du labo B |
| 1.3 ✅ | Comptes, connexion, rôles (gérant, prothésiste, secrétaire, livreur, dentiste), double authentification pour les gérants | Un test par rôle : ce qu'il voit, ce qu'il ne voit pas |
| 1.4 ✅ | Clients : cabinets, praticiens rattachés, adresses de livraison | Création, modification, archivage |
| 1.5 | Catalogue : articles, gammes, prix, TVA par article, grilles tarifaires et remises par client | Test du calcul de prix avec remise client |
| 1.6 ✅ | Internationalisation : sortir les chaînes d'interface des composants vers des fichiers de traduction, traduire les messages de validation en français | Un test qui échoue si une chaîne d'interface est écrite en dur |

> **Dette du lot 1.3 soldée.** Les libellés étaient écrits dans les composants
> React, ce que la règle n° 7 interdit. Ils vivent maintenant dans
> `lang/fr/interface.php`, et un test relit les composants à chaque exécution :
> la dette ne peut plus se reformer sans faire rougir la CI.

> **Le lot 1.4 a été construit avant la phase 0**, à la demande du porteur du
> projet. Chaque hypothèse de modélisation est consignée dans
> [`decisions/002-modele-clients.md`](decisions/002-modele-clients.md), avec le
> coût de sa correction. À reprendre point par point lors de la première visite.

**Fin de phase 1** : un gérant peut créer son labo, ses utilisateurs, ses clients et son catalogue.

---

## Phase 2 — Cœur métier (5 semaines)

| Lot | Contenu | Tests attendus |
|---|---|---|
| 2.1 | Commande (bon de travail) : praticien, référence patient, teinte, articles, date souhaitée | Numérotation unique, sans trou |
| 2.2 | Schéma dentaire (notation FDI, 32 dents) : sélection des dents concernées | Test de sérialisation des dents sélectionnées |
| 2.3 | Étiquette QR code : génération serveur, impression | Le QR code scanné renvoie bien au bon travail |
| 2.4 | Étapes de fabrication paramétrables par labo (plâtre, armature, céramique, finition, contrôle) | Un labo peut ajouter ou retirer une étape |
| 2.5 | Écran de scan atelier : ouverture caméra, scan, validation d'étape en moins d'1 seconde | Test de performance sur l'endpoint de validation |
| 2.6 | Affectation au technicien, planning, alertes de retard | Un travail en retard remonte dans la liste |
| 2.7 | Pièces jointes : photos et fichiers 3D (STL jusqu'à 200 Mo), stockage objet, liens signés | Un lien expiré ne donne plus accès au fichier |

**Fin de phase 2** : un travail peut être saisi, suivi à l'atelier par scan, et terminé.

---

## Phase 3 — Documents (4 semaines)

| Lot | Contenu | Tests attendus |
|---|---|---|
| 3.1 | Traçabilité : matériaux et numéros de lot saisis par étape | Chaque travail terminé a sa liste de matériaux |
| 3.2 | Fiche de traçabilité et déclaration de conformité en PDF | Le PDF contient toutes les mentions validées en phase 0 |
| 3.3 | Bon de livraison, scan à la remise, allers-retours (essayage → retour atelier) | Un travail peut repasser en fabrication sans perdre son historique |
| 3.4 | Facture par travail, numérotation légale continue, immuabilité après émission | Impossible de modifier une facture émise ; correction par avoir uniquement |
| 3.5 | Relevé mensuel par cabinet | Le total du relevé = somme des travaux livrés du mois |
| 3.6 | Modèles PDF personnalisables (logo du labo) | Deux labos différents produisent deux PDF différents |

> **Tous les calculs monétaires sont couverts par des tests automatisés** : remises,
> TVA, avoirs, relevés. Une erreur de facturation fait perdre la confiance du gérant
> immédiatement (§8 du cahier des charges).

---

## Phase 4 — Pilote (2 semaines) → **MVP, ~4 mois**

| Lot | Contenu |
|---|---|
| 4.1 | Import CSV des clients, du catalogue et des tarifs depuis Excel |
| 4.2 | Mise en service chez 1 à 2 laboratoires |
| 4.3 | Corrections issues de l'usage réel (journal des retours dans `docs/pilote/`) |

---

## Phase 5 — V1 (12 semaines)

| Lot | Contenu |
|---|---|
| 5.1 | Extranet praticien : commande en ligne, dépôt de fichiers, suivi temps réel |
| 5.2 | Extranet praticien : téléchargement des documents, messagerie avec le labo |
| 5.3 | Stocks matériaux : entrées, sorties liées aux travaux, lots, péremption, seuils d'alerte |
| 5.4 | Tableau de bord : CA par client et par article, délais moyens, taux de retouches, charge par technicien |
| 5.5 | Tournées de livraison, scan à l'enlèvement et à la livraison |
| 5.6 | Suivi des paiements, relances automatiques |
| 5.7 | Export comptable (FEC / CSV) |
| 5.8 | Facturation électronique : format Factur-X, connexion à une plateforme agréée |
| 5.9 | Abonnements Stripe, espace administrateur de la plateforme |

---

## Phase 6 — Lancement (3 semaines) → **V1, ~7 à 8 mois**

| Lot | Contenu |
|---|---|
| 6.1 | Test d'intrusion par un prestataire externe, corrections |
| 6.2 | Documentation utilisateur et aide en ligne |
| 6.3 | Sauvegardes chiffrées quotidiennes + test de restauration documenté |
| 6.4 | Site commercial et parcours d'inscription |

---

## Phase 7 — V2 (différenciation, après le lancement)

- Lecture automatique des bons de commande papier ou photo par IA.
- Connexion aux scanners intra-oraux et logiciels de CAO dentaire.
- Notifications WhatsApp / SMS au cabinet au départ en livraison.
- Application mobile hors-ligne pour les livreurs.
- Sous-traitance : envoi de travaux à un labo partenaire, suivi des colis, marges.
