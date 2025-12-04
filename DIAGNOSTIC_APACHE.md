# Diagnostic Apache/PHP - DEAL.BZH

## Problème identifié

L'application fonctionne correctement en ligne de commande mais retourne une erreur 500 via Apache, même pour des scripts PHP simples.

## Tests effectués

✅ **En ligne de commande** : L'application s'initialise et fonctionne correctement
❌ **Via Apache** : Tous les scripts PHP retournent une erreur 500

## Causes possibles

1. **PHP-FPM non configuré ou non démarré**
   - Vérifier : `systemctl status php8.4-fpm` (ou version appropriée)
   - Démarrer si nécessaire : `sudo systemctl start php8.4-fpm`

2. **Handler PHP non configuré dans Apache**
   - Vérifier que le module PHP est chargé : `a2enmod php8.4` ou configuration PHP-FPM
   - Vérifier la configuration du VirtualHost

3. **Permissions incorrectes**
   - Le répertoire `/var/www/deal.bzh/web` doit être lisible par l'utilisateur Apache (www-data)
   - Vérifier : `ls -la /var/www/deal.bzh/web`

4. **SELinux ou AppArmor**
   - Vérifier les restrictions de sécurité qui pourraient bloquer l'exécution PHP

5. **Erreur dans la configuration du VirtualHost**
   - Vérifier `/etc/apache2/sites-enabled/deal.bzh.conf`
   - Vérifier que `DocumentRoot` pointe vers `/var/www/deal.bzh/web/public`

## Solutions à essayer

### 1. Vérifier PHP-FPM

```bash
sudo systemctl status php8.4-fpm
sudo systemctl start php8.4-fpm
sudo systemctl enable php8.4-fpm
```

### 2. Vérifier la configuration Apache

```bash
sudo apache2ctl -S  # Voir la configuration des VirtualHosts
sudo apache2ctl configtest  # Tester la configuration
```

### 3. Vérifier les logs Apache

```bash
sudo tail -f /var/log/apache2/error.log
# Puis faire une requête pour voir l'erreur exacte
```

### 4. Vérifier les permissions

```bash
sudo chown -R www-data:www-data /var/www/deal.bzh/web
sudo find /var/www/deal.bzh/web -type d -exec chmod 755 {} \;
sudo find /var/www/deal.bzh/web -type f -exec chmod 644 {} \;
```

### 5. Tester avec un script simple

Créer `/var/www/deal.bzh/web/public/test.php` :
```php
<?php phpinfo();
```

Puis accéder à `http://deal.bzh/test.php`

## Configuration recommandée pour le VirtualHost

```apache
<VirtualHost *:80>
    ServerName deal.bzh
    ServerAlias www.deal.bzh
    
    DocumentRoot /var/www/deal.bzh/web/public
    
    <Directory /var/www/deal.bzh/web/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Pour PHP-FPM
    <FilesMatch \.php$>
        SetHandler "proxy:unix:/var/run/php/php8.4-fpm.sock|fcgi://localhost"
    </FilesMatch>
    
    # Ou pour mod_php
    # (décommenter si vous utilisez mod_php au lieu de PHP-FPM)
    # <FilesMatch \.php$>
    #     SetHandler application/x-httpd-php
    # </FilesMatch>
    
    ErrorLog ${APACHE_LOG_DIR}/deal.bzh_error.log
    CustomLog ${APACHE_LOG_DIR}/deal.bzh_access.log combined
</VirtualHost>
```

## Fichiers de test créés

- `/var/www/deal.bzh/web/public/test-simple.php` - Script PHP minimal
- `/var/www/deal.bzh/web/public/info.php` - phpinfo()
- `/var/www/deal.bzh/web/public/diagnostic.php` - Diagnostic détaillé
- `/var/www/deal.bzh/web/public/error-capture.php` - Capture d'erreurs de l'application

Ces fichiers peuvent être supprimés une fois le problème résolu.

