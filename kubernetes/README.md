# Déploiement Kubernetes - DEAL.BZH

Ce répertoire contient les fichiers de configuration Kubernetes pour déployer l'application DEAL.BZH.

## Prérequis

- Cluster Kubernetes configuré et accessible
- `kubectl` installé et configuré
- Accès à un registry Docker (Docker Hub, GitHub Container Registry, etc.)
- Ingress Controller installé (nginx-ingress recommandé)

## Structure des fichiers

- `deployment.yaml` : Déploiement de l'application PHP-FPM
- `nginx-deployment.yaml` : Déploiement du serveur web Nginx
- `configmap.yaml` : Configuration de l'application et Nginx
- `secret.yaml.example` : Exemple de fichier de secrets (à copier et remplir)
- `ingress.yaml` : Configuration Ingress pour l'accès HTTP/HTTPS
- `mysql-deployment.yaml` : Déploiement MySQL (optionnel, si vous n'utilisez pas de base externe)
- `kustomization.yaml` : Configuration Kustomize pour gérer les déploiements

## Configuration initiale

### 1. Créer les secrets

Copiez `secret.yaml.example` vers `secret.yaml` et remplissez les valeurs :

```bash
cp kubernetes/secret.yaml.example kubernetes/secret.yaml
# Éditez secret.yaml avec vos valeurs
```

**Secrets nécessaires :**
- `db-host` : Adresse du serveur MySQL
- `db-name` : Nom de la base de données
- `db-user` : Utilisateur MySQL
- `db-password` : Mot de passe MySQL
- `mysql-root-password` : Mot de passe root MySQL (si MySQL dans K8s)

### 2. Créer le namespace

```bash
kubectl create namespace deal-bzh
```

### 3. Appliquer les secrets

```bash
kubectl apply -f kubernetes/secret.yaml -n deal-bzh
```

### 4. Construire et pousser l'image Docker

```bash
docker build -t YOUR_REGISTRY/deal-bzh:latest .
docker push YOUR_REGISTRY/deal-bzh:latest
```

### 5. Déployer l'application

```bash
# Avec kustomize
kubectl apply -k kubernetes/ -n deal-bzh

# Ou manuellement
kubectl apply -f kubernetes/deployment.yaml -n deal-bzh
kubectl apply -f kubernetes/nginx-deployment.yaml -n deal-bzh
kubectl apply -f kubernetes/configmap.yaml -n deal-bzh
kubectl apply -f kubernetes/ingress.yaml -n deal-bzh
```

## Configuration GitHub Actions

Le workflow `.github/workflows/deploy.yml` permet un déploiement automatique depuis GitHub.

### Secrets GitHub à configurer

Dans les paramètres du repository GitHub, ajoutez les secrets suivants :

- `REGISTRY_USERNAME` : Nom d'utilisateur du registry Docker
- `REGISTRY_PASSWORD` : Token/mot de passe du registry Docker
- `KUBECONFIG` : Contenu du fichier kubeconfig encodé en base64

Pour encoder votre kubeconfig :
```bash
cat ~/.kube/config | base64 -w 0
```

### Variables d'environnement du workflow

Modifiez dans `.github/workflows/deploy.yml` :

- `REGISTRY` : URL de votre registry Docker
- `KUBERNETES_CONTEXT` : Nom du contexte Kubernetes à utiliser

## Initialisation de la base de données

Après le premier déploiement, exécutez le schéma SQL :

```bash
# Obtenir le nom du pod MySQL
kubectl get pods -n deal-bzh | grep mysql

# Copier le fichier SQL
kubectl cp data/schema.sql deal-bzh-mysql-XXXXX:/tmp/schema.sql -n deal-bzh

# Exécuter le schéma
kubectl exec -it deal-bzh-mysql-XXXXX -n deal-bzh -- mysql -u root -p deal_bzh < /tmp/schema.sql
```

## Vérification du déploiement

```bash
# Vérifier les pods
kubectl get pods -n deal-bzh

# Vérifier les services
kubectl get services -n deal-bzh

# Vérifier l'ingress
kubectl get ingress -n deal-bzh

# Logs de l'application
kubectl logs -f deployment/deal-bzh-app -n deal-bzh

# Logs de Nginx
kubectl logs -f deployment/deal-bzh-nginx -n deal-bzh
```

## Mise à jour de l'application

Pour mettre à jour l'application après un push sur GitHub :

1. Le workflow GitHub Actions construit automatiquement une nouvelle image
2. L'image est poussée vers le registry
3. Le déploiement est mis à jour automatiquement

Pour un déploiement manuel :

```bash
# Reconstruire l'image
docker build -t YOUR_REGISTRY/deal-bzh:VERSION .
docker push YOUR_REGISTRY/deal-bzh:VERSION

# Mettre à jour le déploiement
kubectl set image deployment/deal-bzh-app php-fpm=YOUR_REGISTRY/deal-bzh:VERSION -n deal-bzh
kubectl rollout status deployment/deal-bzh-app -n deal-bzh
```

## Notes importantes

- Les volumes `logs` et `cache` sont en `emptyDir`, les données seront perdues au redémarrage des pods
- Pour la persistance, utilisez des PersistentVolumeClaims
- Ajustez les ressources (CPU/mémoire) selon vos besoins
- Configurez les probes de santé selon votre environnement
- Activez TLS dans l'Ingress pour la production


