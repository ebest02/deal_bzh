<?php
/**
 * Script de test pour vérifier le système de logging
 * À supprimer en production
 */

chdir(dirname(__DIR__));

require_once 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

$logDir = __DIR__ . '/../data/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logger = new Logger('deal_bzh_test');
$logger->pushHandler(new RotatingFileHandler($logDir . '/application.log', 30, Logger::DEBUG));

echo "<h1>Test du système de logging</h1>";
echo "<p>Vérification des logs dans : <code>$logDir</code></p>";

// Test des différents niveaux de log
$logger->debug('Message de debug');
$logger->info('Message d\'information');
$logger->notice('Message de notification');
$logger->warning('Message d\'avertissement');
$logger->error('Message d\'erreur');
$logger->critical('Message critique');
$logger->alert('Message d\'alerte');
$logger->emergency('Message d\'urgence');

echo "<p>✅ Tous les niveaux de log ont été testés.</p>";
echo "<p>Vérifiez le fichier <code>data/logs/application.log</code> pour voir les entrées.</p>";
echo "<p><strong>Note :</strong> Supprimez ce fichier en production.</p>";

