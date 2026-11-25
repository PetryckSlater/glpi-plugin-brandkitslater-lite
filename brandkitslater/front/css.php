<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Endpoint de Entrega de CSS Dinâmico
 * -----------------------------------------------------------------------------
 */

$glpiRoot = dirname(__DIR__, 3);

if (file_exists($glpiRoot . '/vendor/autoload.php')) {
    require_once $glpiRoot . '/vendor/autoload.php';
}

global $DB;
if (!($DB instanceof DBmysql)) {
    $configDir = defined('GLPI_CONFIG_DIR') ? GLPI_CONFIG_DIR : '/etc/glpi';
    if (file_exists($configDir . '/config_db.php')) {
        require_once $configDir . '/config_db.php';
        if (class_exists('DB')) {
            $DB = new DB();
        }
    }
}

require_once __DIR__ . '/../inc/config.class.php';
require_once __DIR__ . '/../inc/cssgenerator.class.php';

header('Content-Type: text/css; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=300'); // 5 minutos de cache no navegador

$config = PluginBrandkitslaterConfig::getConfig();
$type = $_GET['type'] ?? 'login';

if ($type === 'theme') {
    echo PluginBrandkitslaterCssGenerator::generateThemeCss($config);
} else {
    echo PluginBrandkitslaterCssGenerator::generateLoginCss($config);
}
