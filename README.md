[![Codacy Badge](https://app.codacy.com/project/badge/Grade/2a878f8af53a4ae6aad47051316980a1)](https://www.codacy.com/gh/toto93330/AnthonyAlves_7_17052021/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=toto93330/AnthonyAlves_7_17052021&amp;utm_campaign=Badge_Grade)

## CONTEXT : 

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.

## BESOIN CLIENT:

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

 Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de : 

    
    consulter la liste des produits BileMo ;
    consulter les détails d’un produit BileMo ;
    consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
    consulter le détail d’un utilisateur inscrit lié à un client ;
    ajouter un nouvel utilisateur lié à un client ;
    supprimer un utilisateur ajouté par un client.

Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via OAuth ou JWT.

Vous avez le choix entre mettre en place un serveur OAuth et y faire appel (en utilisant le FOSOAuthServerBundle), et utiliser Facebook, Google ou LinkedIn. Si vous décidez d’utiliser JWT, il vous faudra vérifier la validité du token ; l’usage d’une librairie est autorisé.

## Installation : 

Clonez ou téléchargez le repository GitHub dans le dossier voulu :
```sh
git clone https://github.com/toto93330/AnthonyAlves_7_17052021.git
```
Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env.

Téléchargez et installez les dépendances back-end du projet avec Composer :
```sh
composer install
```
Téléchargez et installez les dépendances front-end du projet avec Npm :
```sh
npm install
```
Créer un build d'assets (grâce à Webpack Encore) avec Npm :
```sh
npm run build
```
Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :
```sh
php bin/console doctrine:database:create
```
Créez les différentes tables de la base de données en appliquant les migrations :
```sh
php bin/console doctrine:migrations:migrate
```
(Optionnel) Installer les fixtures pour avoir une démo de données fictives :
```sh
php bin/console doctrine:fixtures:load
```
(Optionnel) Si vous utilisez les fixtures voici l'identifiant administrateur :

```sh
contact@bilmo.com
```
```sh
root
```

Félications le projet est installé correctement, vous pouvez désormais commencer à l'utiliser à votre guise !
