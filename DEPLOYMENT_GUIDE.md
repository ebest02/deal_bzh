# Guide de déploiement - DEAL.BZH

## Configuration du workflow GitHub Actions

### 1. Configuration des secrets GitHub

Allez dans votre repository GitHub : `Settings > Secrets and variables > Actions > New repository secret`

Ajoutez les secrets suivants :

#### REGISTRY_USERNAME
Nom d'utilisateur pour votre registry Docker (ex: pour GitHub Container Registry, utilisez votre nom d'utilisateur GitHub)

#### REGISTRY_PASSWORD
Token d'accès pour votre registry Docker :
- **GitHub Container Registry** : Créez un Personal Access Token avec les permissions `write:packages` et `read:packages`
- **Docker Hub** : Utilisez votre mot de passe Docker Hub ou un token d'accès
- **Autre registry** : Utilisez les identifiants appropriés

#### KUBECONFIG
Contenu de votre fichier kubeconfig encodé en base64 :

```bash
# Sur votre machine locale
cat ~/.kube/config | base64 -w 0
# Copiez le résultat et collez-le dans le secret KUBECONFIG
```

### 2. Configuration des variables d'environnement

Modifiez le fichier `.github/workflows/deploy.yml` :

```yaml
env:
  REGISTRY: ghcr.io  # Exemple pour GitHub Container Registry
  # Ou: docker.io/votre-username
  # Ou: registry.example.com
  
  IMAGE_NAME: deal-bzh
  
  KUBERNETES_NAMESPACE: deal-bzh
  
  KUBERNETES_CONTEXT: votre-contexte-k8s  # Voir avec: kubectl config get-contexts
```

### 3. Configuration Kubernetes

#### Créer les secrets Kubernetes

Créez le fichier `kubernetes/secret.yaml` depuis `secret.yaml.example` :

```bash
cp kubernetes/secret.yaml.example kubernetes/secret.yaml
# Éditez secret.yaml avec vos valeurs
```

Puis appliquez-le :

```bash
kubectl apply -f kubernetes/secret.yaml -n deal-bzh
```

#### Créer le namespace

```bash
kubectl create namespace deal-bzh
```

#### Vérifier la configuration

```bash
# Vérifier que le namespace existe
kubectl get namespace deal-bzh

# Vérifier les secrets
kubectl get secrets -n deal-bzh
```

### 4. Premier déploiement

#### Option A : Via GitHub Actions (recommandé)

1. Poussez votre code sur la branche `main` ou `master`
2. Le workflow se déclenche automatiquement
3. Surveillez l'exécution dans l'onglet `Actions` de GitHub

#### Option B : Déploiement manuel

```bash
# 1. Construire l'image Docker
docker build -t YOUR_REGISTRY/deal-bzh:latest .

# 2. Pousser l'image
docker push YOUR_REGISTRY/deal-bzh:latest

# 3. Mettre à jour le tag dans kustomization.yaml
cd kubernetes
sed -i "s|YOUR_REGISTRY|YOUR_REGISTRY|g" kustomization.yaml

# 4. Déployer
kubectl apply -k .
```

### 5. Vérification du déploiement

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

### 6. Initialisation de la base de données

Après le premier déploiement, initialisez la base de données :

```bash
# Si MySQL est dans Kubernetes
kubectl exec -it deployment/deal-bzh-mysql -n deal-bzh -- mysql -u root -p

# Ou depuis votre machine locale
kubectl port-forward service/mysql-service 3306:3306 -n deal-bzh
mysql -h localhost -P 3306 -u root -p < data/schema.sql
```

### 7. Rollback en cas de problème

```bash
# Voir l'historique des déploiements
kubectl rollout history deployment/deal-bzh-app -n deal-bzh

# Rollback vers la version précédente
kubectl rollout undo deployment/deal-bzh-app -n deal-bzh

# Rollback vers une version spécifique
kubectl rollout undo deployment/deal-bzh-app --to-revision=2 -n deal-bzh
```

## Registries Docker populaires

### GitHub Container Registry (ghcr.io)

```yaml
REGISTRY: ghcr.io
REGISTRY_USERNAME: votre-username-github
REGISTRY_PASSWORD: ghp_xxxxxxxxxxxxx  # Personal Access Token
```

L'image sera disponible à : `ghcr.io/votre-username/deal-bzh:tag`

### Docker Hub

```yaml
REGISTRY: docker.io
REGISTRY_USERNAME: votre-username-dockerhub
REGISTRY_PASSWORD: votre-password-ou-token
```

L'image sera disponible à : `docker.io/votre-username/deal-bzh:tag`

### Registry privé

```yaml
REGISTRY: registry.example.com
REGISTRY_USERNAME: votre-username
REGISTRY_PASSWORD: votre-password
```

## Dépannage

### Le workflow échoue à l'étape "Build and push"

- Vérifiez que les secrets `REGISTRY_USERNAME` et `REGISTRY_PASSWORD` sont corrects
- Vérifiez que le registry accepte les connexions depuis GitHub Actions
- Vérifiez les logs du workflow dans GitHub

### Le workflow échoue à l'étape "Deploy"

- Vérifiez que le secret `KUBECONFIG` est correct et encodé en base64
- Vérifiez que le contexte Kubernetes existe : `kubectl config get-contexts`
- Vérifiez que vous avez les permissions nécessaires sur le cluster

### Les pods ne démarrent pas

- Vérifiez les logs : `kubectl logs -n deal-bzh -l app=deal-bzh`
- Vérifiez les événements : `kubectl get events -n deal-bzh --sort-by='.lastTimestamp'`
- Vérifiez les secrets : `kubectl get secrets -n deal-bzh`

### L'image n'est pas trouvée

- Vérifiez que l'image a bien été poussée : `docker pull YOUR_REGISTRY/deal-bzh:tag`
- Vérifiez les permissions du registry
- Vérifiez que le tag d'image dans `deployment.yaml` correspond

## Ressources supplémentaires

- [Documentation Kubernetes](https://kubernetes.io/docs/)
- [Documentation Kustomize](https://kustomize.io/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Docker Documentation](https://docs.docker.com/)

