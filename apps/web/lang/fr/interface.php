<?php

declare(strict_types=1);

/**
 * Toutes les chaînes affichées à l'écran.
 *
 * Règle n° 7 du projet : aucune chaîne d'interface n'est écrite dans un
 * composant. Ce fichier est partagé avec le front par Inertia, et lu côté
 * React avec le crochet useTraduction().
 *
 * Pour ajouter une langue, dupliquer ce fichier dans lang/<langue>/.
 */
return [

    'commun' => [
        'seDeconnecter' => 'Se déconnecter',
        'roleNonAttribue' => 'rôle non attribué',
    ],

    'accueil' => [
        'presentation' => 'Plateforme de gestion pour laboratoires de prothèses dentaires : commandes, fabrication, traçabilité, livraison et facturation.',
        'cadrage' => 'Le projet est en phase de cadrage. Le périmètre et le découpage en lots sont décrits dans :cahier et :plan.',
        'version' => 'Version :version',
    ],

    'connexion' => [
        'invitation' => 'Connectez-vous à votre laboratoire.',
        'email' => 'Adresse e-mail',
        'motDePasse' => 'Mot de passe',
        'resterConnecte' => 'Rester connecté',
        'seConnecter' => 'Se connecter',
    ],

    'defi' => [
        'titre' => 'Vérification',
        'consigneApplication' => 'Saisissez le code affiché par votre application d’authentification.',
        'consigneSecours' => 'Saisissez un de vos codes de secours.',
        'code' => 'Code à six chiffres',
        'codeDeSecours' => 'Code de secours',
        'verifier' => 'Vérifier',
        'utiliserApplication' => 'Utiliser le code de mon application',
        'utiliserSecours' => 'Utiliser un code de secours',
    ],

    'confirmationMotDePasse' => [
        'titre' => 'Confirmez votre mot de passe',
        'explication' => 'Cette opération touche à la sécurité de votre compte.',
        'motDePasse' => 'Mot de passe',
        'confirmer' => 'Confirmer',
    ],

    'doubleAuthentification' => [
        'titre' => 'Double authentification',
        'exigee' => 'Votre rôle donne accès à la facturation et aux paramètres du laboratoire. La double authentification est obligatoire pour continuer.',
        'exigeeParVotreRole' => 'Votre rôle exige la double authentification. Activez-la pour continuer.',
        'principe' => 'Un code à six chiffres, changeant toutes les trente secondes, vous sera demandé à chaque connexion. Il faut une application d’authentification sur votre téléphone.',
        'activer' => 'Activer la double authentification',
        'consigneScan' => 'Scannez ce code avec votre application d’authentification, puis saisissez le code à six chiffres qu’elle affiche.',
        'preparationDuCode' => 'Préparation du code…',
        'code' => 'Code à six chiffres',
        'confirmer' => 'Confirmer',
        'active' => 'La double authentification est active sur votre compte.',
        'codesDeSecours' => 'Codes de secours',
        'codesDeSecoursExplication' => 'Conservez-les hors de votre téléphone. Ils sont votre seul moyen d’entrer si vous le perdez, et chacun ne sert qu’une fois.',
        'desactiver' => 'Désactiver',
        'invitation' => 'Activer la double authentification',
        'invitationSuite' => 'pour protéger votre compte.',
    ],

    'tableauDeBord' => [
        'titre' => 'Tableau de bord',
        'salutation' => 'Bonjour :nom. Vous êtes connecté en tant que',
        'auLaboratoire' => 'au laboratoire :laboratoire',
        'sansRole' => 'utilisateur sans rôle attribué',
        'aucunEcran' => 'Aucun écran ne vous est ouvert pour l’instant. Si c’est inattendu, demandez au gérant de vérifier votre rôle.',
        'cePourquoiVousEtesHabilite' => 'Ce que vous pouvez faire',
    ],

    'chantier' => [
        'explication' => 'Cet écran reste à construire : il fait partie du lot :lot du plan de travail. Vous y avez accès, c’est déjà ce qui est vérifié ici.',
    ],

    'clients' => [

        'titre' => 'Cabinets clients',
        'nouveau' => 'Nouveau cabinet',
        'modifier' => 'Modifier',
        'enregistrer' => 'Enregistrer',
        'annuler' => 'Annuler',
        'archiver' => 'Archiver',
        'restaurer' => 'Restaurer',
        'ajouter' => 'Ajouter',
        'retourALaListe' => 'Retour à la liste',
        'voirLesArchives' => 'Voir les cabinets archivés',
        'voirLesActifs' => 'Voir les cabinets actifs',
        'aucunCabinet' => 'Aucun cabinet pour l’instant. Commencez par en créer un, ou importez votre fichier client (lot 4.1).',
        'aucunCabinetArchive' => 'Aucun cabinet archivé.',
        'estArchive' => 'Ce cabinet est archivé. Il n’apparaît plus dans les listes, mais son historique est conservé.',
        'nombreDePraticiens' => ':nombre praticien|:nombre praticiens',
        'confirmationArchivage' => 'Archiver ce cabinet ? Il disparaîtra des listes, mais ses travaux et ses factures sont conservés.',

        'titreCreation' => 'Nouveau cabinet',
        'titreModification' => 'Modifier :cabinet',

        'sections' => [
            'identite' => 'Identité',
            'coordonnees' => 'Coordonnées',
            'facturation' => 'Adresse de facturation',
            'reglement' => 'Règlement',
            'praticiens' => 'Praticiens',
            'adresses' => 'Adresses de livraison',
            'notes' => 'Notes',
        ],

        'aucunPraticien' => 'Aucun praticien rattaché à ce cabinet.',
        'aucuneAdresse' => 'Aucune adresse de livraison. Les travaux seront livrés à l’adresse de facturation.',
        'adressePrincipale' => 'Adresse principale',
        'archives' => 'Archivés',

        'champs' => [
            'cabinet' => [
                'raison_sociale' => 'raison sociale',
                'siret' => 'SIRET',
                'numero_tva' => 'numéro de TVA',
                'email' => 'adresse e-mail',
                'telephone' => 'téléphone',
                'adresse_ligne_1' => 'adresse',
                'adresse_ligne_2' => 'complément d’adresse',
                'code_postal' => 'code postal',
                'ville' => 'ville',
                'pays' => 'pays',
                'delai_reglement_jours' => 'délai de règlement',
                'notes' => 'notes',
            ],
            'praticien' => [
                'civilite' => 'civilité',
                'nom' => 'nom',
                'prenom' => 'prénom',
                'numero_rpps' => 'numéro RPPS',
                'email' => 'adresse e-mail',
                'telephone' => 'téléphone',
            ],
            'adresse' => [
                'libelle' => 'libellé',
                'adresse_ligne_1' => 'adresse',
                'adresse_ligne_2' => 'complément d’adresse',
                'code_postal' => 'code postal',
                'ville' => 'ville',
                'pays' => 'pays',
                'contact' => 'contact sur place',
                'telephone' => 'téléphone',
                'instructions' => 'instructions de livraison',
                'est_principale' => 'adresse principale',
            ],
        ],

        'aides' => [
            'delai_reglement_jours' => 'En jours, à compter de la date de facture.',
            'numero_rpps' => 'Onze chiffres, facultatif.',
            'instructions' => 'Étage, code d’accès, horaires de réception…',
        ],

        'messages' => [
            'cabinetCree' => 'Le cabinet a été créé.',
            'cabinetModifie' => 'Le cabinet a été modifié.',
            'cabinetArchive' => 'Le cabinet a été archivé.',
            'cabinetRestaure' => 'Le cabinet a été restauré.',
            'praticienAjoute' => 'Le praticien a été ajouté.',
            'praticienModifie' => 'Le praticien a été modifié.',
            'praticienArchive' => 'Le praticien a été archivé.',
            'praticienRestaure' => 'Le praticien a été restauré.',
            'adresseAjoutee' => 'L’adresse de livraison a été ajoutée.',
            'adresseModifiee' => 'L’adresse de livraison a été modifiée.',
            'adresseArchivee' => 'L’adresse de livraison a été archivée.',
            'adresseRestauree' => 'L’adresse de livraison a été restaurée.',
        ],

    ],

    'menu' => [
        'tableauDeBord' => 'Tableau de bord',
        'clients' => 'Clients',
        'nouvelleCommande' => 'Nouvelle commande',
        'atelier' => 'Atelier',
        'tournee' => 'Ma tournée',
        'mesTravaux' => 'Mes travaux',
        'facturation' => 'Facturation',
        'parametres' => 'Paramètres',
    ],

    'ecrans' => [
        'parametres' => 'Paramètres du laboratoire',
        'facturation' => 'Facturation',
        'nouvelleCommande' => 'Nouvelle commande',
        'atelier' => 'Atelier — validation des étapes',
        'tournee' => 'Ma tournée',
        'mesTravaux' => 'Mes travaux',
    ],

];
