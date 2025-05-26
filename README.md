# weblog-v0

# penser à la validation des posts par l'admin
# tables d'archives ?
# voir navbar, news contact about
# messages et notif (bonus)

DRAFT Reprise du projet « weblog »
Rapport du 24/05/2025 par Mikael Debray
Weblog v0 semble être un projet de publication d’articles qui n’a jamais abouti.
Il présente à son état initial des anomalies et un manque de fonctionnalités essentielles à son fonctionnement normal. Ce rapport présente de façon synthétique les changements apportés pour faire fonctionner le site.
Technologies utilisées :

php, html, css 
Ckeditor
Mysql pour la base

Vue d’ensemble du site dans son état initial (V0) :
*voir pour un diagramme
FRONT
BACK

Base de données :
Le fichier weblog.sql permet de créer la base. Cette dernière est relativement bien construite mais elle est imparfaite.
 

Elle se compose des tables posts, users, topics, roles, 
Et de la table de jointure post_topics
En commentaire il est suggéré de créer la table de jointure supplémentaire role_user pour joindre les id des tables users et roles en obtenant les colonnes suivantes :
role_user(id, user_id,role_id)
Certaines tables (post_topic, roles, topics, users, posts) ont des champs id mais la configuration AUTO_INCREMENT et la clé primaire n’est pas correctement définies.
D’autre commentaires laissent entendre que la création de tables d’archive des posts et users supprimées serait souhaités


Points notables :
L’architecture des dossiers..
Il existe des fichiers de style css pour styliser le site via un header
L’id dans la table post_topic n’est pas auto incrémentée.
Composants absents :
La logique métier est pratiquement inexistante.

•	Il n’y pas de système d’enregistrement pour l’utilisateur :
	Register.php est vide
•	Les fichiers admin_function.php, post_functions.php et all_functions.php sont vide. La logique métier est donc quasiment inexistante. Il manque toutes les fonctionnalités liées à la publication des articles, la validation des articles par l’administrateur, leur edition
•	Le renvoi d’erreur est inexistant, le fichier errors.php est vide.
•	La page principale index.php présente un section « Articles Récent » mais elle est vide.
•	Le fichier single_post est vide, il doit permettre d’afficher l’article complet.
•	La fichier filtered_post est vide.  Cette page devrait pouvoir montrer les articles et permettre de les filtrer par sujet voir par auteur.



Composant présent :
•	La page user.php existe et propose une interface avec listing, création, édition et suppression mais la logique est absente
•	Le fichier Create_post possède déjà la logique d’upload d’image et d’édition. Elle fonctionne avec CKeditor qui est déjà présent dans le projet.
Cependant la logique d’enregistrement est à créer
•	Un tableau de bord pour l’administrateur est présent, et dirige l’administrateur vers la page user et post.php uniquement. De plus l’incrémentation des nombres de post, d’user et de posts publié est inexistante. 
 
Anomalie :
Il y a une coquille sur la constante root_path dans le fichier config _FILE_ 
 
Actions effectuées :
Durant toute l’optimisation du site la charte graphique a été respectée en réutilisant les fichiers de style css déjà présent à l’état initial.  admin_styling.css ou public_styling.css
Prérequis : En premier lieu on crée la base via le fichier fournit weblog.sql
Il faut modifier la page config.php de façon à pouvoir se connecter à la base de données : 
Rentrer le nom de la base, le login et le mot de passe de connexion :
BASE : weblog
Login : root
mot de passe : aaaaaaaa

Les fonctions sont placées de façon à préserver la logique initiale dans admin_functions.php All_functions.php et post_funciton.php.

•	Correction de l’anomalie dans le root_path du fichier config.php 
_FILE_  __FILE__
 

1) Modification dans la base de données :
•	Les droits des utilisateurs passent désormais par la table role_user :
o	Création d’une table de jointure role_user 
o	Suppression du champ role dans users
o	Migration des anciens rôles utilisateurs dans la nouvelle table role_user
•	Ajout (ou correction) de la clause AUTO_INCREMENT et définition correcte des clés primaires pour chaque table.
•	Ajout des index et des clés étrangères pour garantir l’intégrité et éviter les duplications.

2) Ajout des composants :
•	La page d’accueil doit afficher les articles, ajout d’une fonction getPublishedPosts() pour récupérer les articles publiés en commençant par les plus récent.
 
•	Affichage du détails du post en cliquant sur « Lire la suite »
 
•	Affichage des post pour permettre à l’admin de les valider ou de les refuser. Ajout de getAllPosts() et création de la page posts.php
o	Possibilité de supprimer un post
o	Possibilité de modifier un post (voir encore les cas speciaux si temps)

•	Création et édition de post dans create_post.php
•	Ajout d’une gestion pour les messages d’erreur dans errors.php
•	Verrouillage des pages admin avec la vérification du rôle pour la session et renvoi automatique vers la page d’identification si l’utilisateur n’est pas admin.
•	Un auteur ne peut modifier que ses post, et n’a pas accès aux pages de gestion utilisateur et de gestion des topics qui sont réservé aux admin.
•	Page filtered_post pour afficher tout les posts et pouvoir les filtrer



Notes :
 
On a cré un compte et ce dernier est enregistré dans la base :
 




Note soutenance :
Commencer par montrer enregistrement et login, puis qu’il est impossible d’acceder aux pages de l’administrateur sans être admin
