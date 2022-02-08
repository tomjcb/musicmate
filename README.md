# Description

Ce Site Web a été développé en solitaire dans le cadre du Projet de Fin d'Etudes (PFE) du DUT Informatique. Ce dernier remplace le stage de fin de deuxième année, à cause des restrictions sanitaires dues au COVID.
J'ai décidé de développer un site web en Symfony. Ce dernier est un réseau social, qui est orienté sur les goûts musicaux des utilisateurs

Il a été développé en Php à l'aide du Framework Symfony.

# Config pour démarrer le site

## Configurer le fichier .env

```php
# .env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

Afin de configurer la base de données qui va être utilisée, il faut remplacer les champs *db_user*, *db_password* et *db_name* par ce que vous utilisez comme user et db.

## Commandes à effectuer
> Les commandes doivent être entrées, depuis le dossier racine du projet (celui qui contient bin, config, public etc.)

### Générer les dossier var et vendor (il faut obligatoirement les avoir)
Il faut tout d'abord avoir composer d'installé.
Ensuie, il faut executer la commande 'composer install' afin que les dossiers var et vendor soit générés

### Injecter les tables dans la base de donnée indiquée dans le .env

```
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
```
### Injecter les fixtures dans la base de données

```
php bin/console doctrine:fixtures:load
```

### Démarrer le serveur sous localhost
```
php -S localhost:8000 -t public
```

> Si le serveur n'est pas démarré sous *localhost*, les seules fonctionnalitées qui ne marcheront pas, seront l'inscription par google et facebook, car la redirection après login sur ces deux plateformes, se fait avec l'url basée sur localhost.
