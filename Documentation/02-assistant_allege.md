# Assistant allégé

## Permissions

L'assistant allégé permettant de modifier certains paramètres d'un cours est accessible sur chaque page de cours depuis le menu 
*Navigation ► Assistant création de cours ► Assistant paramétrage*. 

Ce lien n'apparaît que pour les personnes ayant le droit de gérer les inscriptions dans le contexte d'un cours.

Le lien redirige vers la page moodle standard de modification des paramètres si le cours n'a pas été créé au moyen de l'assistant.

Une nouvelle capacité "Modifier les rattachements au ROF d'un cours" (`rofreferenceeditor`) a été ajoutée, par défaut accordée au rôle "Gestionnaire". 
Sur l'instance de test, nous avons créé le rôle système "EditorRof Système (via assistant)" qui permet de donner la nouvelle capacité à des utilisateurs pour l'ensemble des cours de la plate-forme.


## Fonctionnalités

### Pour le cas 2 (ROF) :

 1.  la page "étape 2 : Identification de l'espace de cours" :
    * entièrement modifiable pour ceux ayant le droit de modifier le rattachement au ROF (`rofreferenceeditor`),
    * seulement les champs "complément du nom de l'espace de cours", "texte de présentation", "date de début" et "date de fin" pour les autres.
 2.  la page étape 5 : inscription des utilisateurs à l'étape de cours (étudiants),
 3.  la page étape 6 : clé d'inscription,
 4.  une version modifiée de l'étape 7 : récapitulatif et enregistrement des modifications.

### Pour le cas 3 (hors ROF) :

 1.  étape 2 : identification de l'espace de cours,
 2.  étape 3 : description de l'espace de cours,
 3.  étape 5 : inscription des utilisateurs à l'étape de cours (étudiants),
 4.  la page étape 6 : clé d'inscription
 5.  une version modifiée de l'étape 7 : récapitulatif et enregistrement des modifications. 
