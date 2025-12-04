# DEAL.BZH - Plateforme d'échange et de partage de matériel

Application web développée avec le framework Laminas MVC pour l'échange et le partage de matériel de sonorisation, vidéo, informatique old school, etc.

## Installation

1. Installer les dépendances Composer :
```bash
composer install
```

2. Configurer la base de données :
   - Créer la base de données MySQL
   - Copier `config/autoload/local.php.dist` vers `config/autoload/local.php`
   - Modifier les paramètres de connexion dans `config/autoload/local.php`
   - Exécuter le script SQL : `data/schema.sql`

3. Configurer le serveur web :
   - Pointer le document root vers le dossier `public/`
   - Activer la réécriture d'URL (mod_rewrite pour Apache)

## Structure des modules

- **Application** : Module core avec layout et routes de base
- **User** : Authentification, gestion des profils et système de notation
- **Deal** : Gestion des annonces, catégories, favoris
- **Message** : Messagerie interne entre utilisateurs
- **Admin** : Administration et modération de la plateforme

## Fonctionnalités

- Authentification utilisateur (inscription, connexion)
- Gestion des annonces (CRUD)
- Catégories de matériel
- Recherche et filtres
- Système de favoris
- Messagerie interne
- Système de notation des utilisateurs
- Administration et modération
- Charte graphique basée sur le logo DEAL.BZH

## Configuration

Les fichiers de configuration se trouvent dans `config/autoload/` :
- `global.php` : Configuration globale
- `local.php` : Configuration locale (base de données, etc.) - à créer depuis `local.php.dist`

## Développement

Pour lancer le serveur de développement :
```bash
composer serve
```

L'application sera accessible sur `http://localhost:8080`

