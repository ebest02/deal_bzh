# Logs de l'application DEAL.BZH

Ce dossier contient les logs de l'application.

## Fichiers de logs

- **application.log** : Logs généraux de l'application (niveau DEBUG et supérieur)
- **error.log** : Logs des erreurs uniquement (niveau ERROR et supérieur)

## Rotation des logs

Les logs sont automatiquement rotatés :
- Un nouveau fichier est créé chaque jour
- Les anciens fichiers sont conservés pendant 30 jours
- Format : `application.log`, `application.log.1`, `application.log.2`, etc.

## Niveaux de log

- **DEBUG** : Informations détaillées pour le débogage
- **INFO** : Informations générales sur le fonctionnement de l'application
- **NOTICE** : Notifications importantes
- **WARNING** : Avertissements (erreurs non critiques)
- **ERROR** : Erreurs qui nécessitent une attention
- **CRITICAL** : Erreurs critiques
- **ALERT** : Alertes nécessitant une action immédiate
- **EMERGENCY** : Urgences système

## Permissions

Assurez-vous que le serveur web a les permissions d'écriture sur ce dossier :
```bash
chmod 755 data/logs
chown www-data:www-data data/logs
```

