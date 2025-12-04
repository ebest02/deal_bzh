# Configuration des variables d'environnement - DEAL.BZH

## Fichier .env

Tous les mots de passe, clés, tokens et configurations sensibles sont stockés dans le fichier `.env` à la racine du projet.

## Installation

1. **Copier le fichier d'exemple** :
   ```bash
   cp .env.example .env
   ```

2. **Éditer le fichier .env** et remplir toutes les valeurs :
   ```bash
   nano .env
   # ou
   vim .env
   ```

3. **Vérifier que .env est dans .gitignore** (déjà configuré) :
   ```bash
   cat .gitignore | grep .env
   ```

## Variables importantes

### Base de données
- `DB_HOST` : Adresse du serveur MySQL
- `DB_PORT` : Port MySQL (par défaut: 3306)
- `DB_NAME` : Nom de la base de données
- `DB_USER` : Utilisateur MySQL
- `DB_PASSWORD` : Mot de passe MySQL

### Docker & Kubernetes
- `DOCKER_REGISTRY` : Registry Docker (ex: `ghcr.io`, `docker.io`)
- `DOCKER_IMAGE_NAME` : Nom de l'image Docker
- `KUBERNETES_NAMESPACE` : Namespace Kubernetes
- `KUBERNETES_CONTEXT` : Contexte Kubernetes

### GitHub Actions
- `GITHUB_REGISTRY_USERNAME` : Nom d'utilisateur GitHub
- `GITHUB_REGISTRY_TOKEN` : Personal Access Token GitHub
- `DOCKER_HUB_USERNAME` : Nom d'utilisateur Docker Hub (si utilisé)
- `DOCKER_HUB_TOKEN` : Token Docker Hub (si utilisé)
- `KUBECONFIG_BASE64` : Kubeconfig encodé en base64

Pour générer le KUBECONFIG_BASE64 :
```bash
cat ~/.kube/config | base64 -w 0
```

## Scripts utilitaires

### Générer le secret Kubernetes

Génère le fichier `kubernetes/secret.yaml` depuis `.env` :

```bash
./scripts/generate-k8s-secret.sh
```

Puis appliquez-le :
```bash
kubectl apply -f kubernetes/secret.yaml -n deal-bzh
```

### Configurer les secrets GitHub Actions

Configure automatiquement les secrets GitHub depuis `.env` (nécessite GitHub CLI) :

```bash
./scripts/setup-github-secrets.sh
```

Ou manuellement via l'interface GitHub :
1. Allez dans `Settings > Secrets and variables > Actions`
2. Ajoutez les secrets suivants :
   - `REGISTRY_USERNAME`
   - `REGISTRY_PASSWORD`
   - `KUBECONFIG`

## Utilisation dans le code

Les variables d'environnement sont automatiquement chargées au démarrage de l'application via `src/Config/EnvLoader.php`.

Dans le code PHP :
```php
// Via getenv()
$dbHost = getenv('DB_HOST');

// Via $_ENV
$dbHost = $_ENV['DB_HOST'];

// Via EnvLoader
$dbHost = \Application\Config\EnvLoader::get('DB_HOST', 'localhost');
```

## Sécurité

⚠️ **IMPORTANT** :
- Ne commitez JAMAIS le fichier `.env` dans Git
- Le fichier `.env` est déjà dans `.gitignore`
- Utilisez `.env.example` comme template pour les autres développeurs
- Changez tous les mots de passe par défaut en production
- Utilisez des mots de passe forts et uniques

## Génération de clés sécurisées

Pour générer des clés sécurisées :

```bash
# Clé de chiffrement (32 caractères)
openssl rand -base64 32

# Clé CSRF (32 caractères)
openssl rand -base64 32

# Mot de passe MySQL root
openssl rand -base64 24
```

## Variables d'environnement dans Kubernetes

Les variables d'environnement sont injectées dans les pods Kubernetes via les secrets. Le script `generate-k8s-secret.sh` génère automatiquement le fichier `secret.yaml` depuis `.env`.

## Variables d'environnement dans Docker

Le Dockerfile charge automatiquement les variables d'environnement. Assurez-vous de passer les variables nécessaires lors du build ou de l'exécution :

```bash
# Build avec variables
docker build --build-arg DB_HOST=mysql .

# Run avec variables
docker run -e DB_HOST=mysql -e DB_PASSWORD=secret myapp
```

Ou utilisez un fichier `.env` avec docker-compose ou `--env-file`.

