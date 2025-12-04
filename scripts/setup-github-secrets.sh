#!/bin/bash

# Script pour configurer les secrets GitHub Actions depuis .env
# Nécessite GitHub CLI (gh) installé

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$PROJECT_ROOT/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "Erreur: Le fichier .env n'existe pas à $ENV_FILE"
    exit 1
fi

# Vérifier que gh CLI est installé
if ! command -v gh &> /dev/null; then
    echo "Erreur: GitHub CLI (gh) n'est pas installé"
    echo "Installez-le avec: https://cli.github.com/"
    exit 1
fi

# Charger les variables depuis .env
source <(grep -v '^#' "$ENV_FILE" | grep -v '^$' | sed 's/^/export /')

echo "Configuration des secrets GitHub Actions..."
echo ""

# Déterminer le registry à utiliser
if [ -n "$DOCKER_REGISTRY" ] && [ "$DOCKER_REGISTRY" != "YOUR_REGISTRY" ]; then
    if [[ "$DOCKER_REGISTRY" == *"ghcr.io"* ]] || [[ "$DOCKER_REGISTRY" == *"github.com"* ]]; then
        REGISTRY_USERNAME="${GITHUB_REGISTRY_USERNAME}"
        REGISTRY_PASSWORD="${GITHUB_REGISTRY_TOKEN}"
    else
        REGISTRY_USERNAME="${DOCKER_HUB_USERNAME}"
        REGISTRY_PASSWORD="${DOCKER_HUB_TOKEN}"
    fi
else
    echo "⚠️  DOCKER_REGISTRY n'est pas configuré dans .env"
    echo "Utilisation des valeurs par défaut (GitHub Container Registry)"
    REGISTRY_USERNAME="${GITHUB_REGISTRY_USERNAME}"
    REGISTRY_PASSWORD="${GITHUB_REGISTRY_TOKEN}"
fi

# Configurer les secrets
if [ -n "$REGISTRY_USERNAME" ] && [ "$REGISTRY_USERNAME" != "YOUR_GITHUB_USERNAME" ]; then
    echo "Setting REGISTRY_USERNAME..."
    gh secret set REGISTRY_USERNAME --body "$REGISTRY_USERNAME"
fi

if [ -n "$REGISTRY_PASSWORD" ] && [ "$REGISTRY_PASSWORD" != "ghp_YOUR_PERSONAL_ACCESS_TOKEN" ]; then
    echo "Setting REGISTRY_PASSWORD..."
    gh secret set REGISTRY_PASSWORD --body "$REGISTRY_PASSWORD"
fi

if [ -n "$KUBECONFIG_BASE64" ] && [ "$KUBECONFIG_BASE64" != "YOUR_KUBECONFIG_BASE64" ]; then
    echo "Setting KUBECONFIG..."
    gh secret set KUBECONFIG --body "$KUBECONFIG_BASE64"
else
    echo "⚠️  KUBECONFIG_BASE64 n'est pas configuré dans .env"
    echo "Générez-le avec: cat ~/.kube/config | base64 -w 0"
fi

echo ""
echo "✅ Secrets GitHub Actions configurés avec succès"
echo ""
echo "Vérifiez les secrets avec:"
echo "  gh secret list"

