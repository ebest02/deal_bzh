# GitHub Actions Workflows - DEAL.BZH

## Workflow de déploiement

Le workflow `deploy.yml` automatise la construction de l'image Docker et le déploiement sur Kubernetes.

## Configuration requise

### Secrets GitHub

Dans les paramètres du repository GitHub (`Settings > Secrets and variables > Actions`), configurez les secrets suivants :

1. **REGISTRY_USERNAME** : Nom d'utilisateur pour le registry Docker
2. **REGISTRY_PASSWORD** : Token/mot de passe pour le registry Docker
3. **KUBECONFIG** : Contenu du fichier kubeconfig encodé en base64

Pour encoder votre kubeconfig :
```bash
cat ~/.kube/config | base64 -w 0
```

### Variables d'environnement

Modifiez dans `.github/workflows/deploy.yml` :

- **REGISTRY** : URL de votre registry Docker (ex: `ghcr.io`, `docker.io/username`, `registry.example.com`)
- **KUBERNETES_CONTEXT** : Nom du contexte Kubernetes à utiliser

Pour voir vos contextes Kubernetes :
```bash
kubectl config get-contexts
```

## Fonctionnement

### Job 1: Build and Push

1. Checkout du code
2. Configuration de Docker Buildx
3. Connexion au registry Docker
4. Extraction des métadonnées (tags)
5. Construction et push de l'image Docker
6. Génération du tag d'image (SHA du commit)

### Job 2: Deploy

1. Checkout du code
2. Installation de kubectl et kustomize
3. Configuration de kubectl avec le kubeconfig
4. Création du namespace si nécessaire
5. Mise à jour des tags d'image dans les manifests Kubernetes
6. Déploiement avec kustomize
7. Vérification du statut des déploiements
8. Affichage des logs en cas d'échec

## Déclenchement

Le workflow se déclenche automatiquement sur :
- Push sur les branches `main` ou `master`
- Déclenchement manuel via `workflow_dispatch`

## Tags d'image

Les images sont taguées avec :
- `latest` : Pour la branche principale
- `{branch}-{sha}` : Pour les autres branches
- `{sha}` : SHA du commit

## Rollback

En cas de problème, vous pouvez rollback avec :

```bash
kubectl rollout undo deployment/deal-bzh-app -n deal-bzh
kubectl rollout undo deployment/deal-bzh-nginx -n deal-bzh
```

Ou déployer une version spécifique :

```bash
kubectl set image deployment/deal-bzh-app php-fpm=REGISTRY/deal-bzh:SHA -n deal-bzh
```

