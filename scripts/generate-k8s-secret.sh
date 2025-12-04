#!/bin/bash

# Script pour générer le fichier secret.yaml Kubernetes depuis .env

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$PROJECT_ROOT/.env"
SECRET_FILE="$PROJECT_ROOT/kubernetes/secret.yaml"

if [ ! -f "$ENV_FILE" ]; then
    echo "Erreur: Le fichier .env n'existe pas à $ENV_FILE"
    echo "Copiez .env.example vers .env et remplissez les valeurs"
    exit 1
fi

# Charger les variables depuis .env
source <(grep -v '^#' "$ENV_FILE" | grep -v '^$' | sed 's/^/export /')

# Créer le fichier secret.yaml
cat > "$SECRET_FILE" <<EOF
apiVersion: v1
kind: Secret
metadata:
  name: deal-bzh-secrets
  labels:
    app: deal-bzh
type: Opaque
stringData:
  db-host: "${DB_HOST:-mysql-service}"
  db-name: "${DB_NAME:-deal_bzh}"
  db-user: "${DB_USER:-deal_bzh}"
  db-password: "${DB_PASSWORD}"
  mysql-root-password: "${MYSQL_ROOT_PASSWORD:-}"
EOF

echo "✅ Fichier secret.yaml généré avec succès: $SECRET_FILE"
echo "⚠️  Vérifiez le contenu avant de l'appliquer à Kubernetes"
echo ""
echo "Pour appliquer:"
echo "  kubectl apply -f $SECRET_FILE -n ${KUBERNETES_NAMESPACE:-deal-bzh}"

