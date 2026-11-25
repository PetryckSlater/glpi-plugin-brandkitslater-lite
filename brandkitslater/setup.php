<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater (Community Edition) para GLPI 10 e 11
 * Autor: Petryck Slater
 * Finalidade: Personalização Visual Completa, Layouts de Login, Cores e Temas
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

define('PLUGIN_BRANDKITSLATER_VERSION', '1.0.0-lite');
define('PLUGIN_BRANDKITSLATER_MIN_GLPI', '10.0.0');

/**
 * Inicialização e registro dos Hooks do Plugin
 */
function plugin_init_brandkitslater() {
    global $PLUGIN_HOOKS;

    // Conformidade com proteção CSRF do GLPI
    $PLUGIN_HOOKS['csrf_compliant']['brandkitslater'] = true;

    // Registro da página de configuração no menu Configurar > Plugins
    $PLUGIN_HOOKS['config_page']['brandkitslater'] = 'front/config.form.php';

    // Firewall do GLPI 11 para execução autenticada de scripts de front
    if (class_exists('Glpi\Http\Firewall') && method_exists('Glpi\Http\Firewall', 'addPluginStrategyForLegacyScripts')) {
        \Glpi\Http\Firewall::addPluginStrategyForLegacyScripts(
            'brandkitslater',
            '#^/front/.*\.php$#',
            \Glpi\Http\Firewall::STRATEGY_AUTHENTICATED
        );
    }

    // Injeção de CSS na Tela de Login (Página Anônima)
    $PLUGIN_HOOKS['add_css_anonymous_page']['brandkitslater'] = 'css/brandkit_login.css';

    // Injeção de CSS no Painel Interno: somente se explicitamente habilitado nas configurações
    if (file_exists(__DIR__ . '/inc/config.class.php')) {
        require_once __DIR__ . '/inc/config.class.php';
        $brandkitConfig = PluginBrandkitslaterConfig::getConfig();
        if (!empty($brandkitConfig['enable_internal_theme'])) {
            $PLUGIN_HOOKS['add_css']['brandkitslater'] = 'css/brandkit_theme.css';
        }
    }

    // Injeção de JavaScript para manipulação de Favicon, Logotipos e Ícones Dinâmicos
    $jsFiles = ['js/brandkit_data.js', 'js/brandkit_icons.js'];
    $PLUGIN_HOOKS['add_javascript']['brandkitslater'] = $jsFiles;
    $PLUGIN_HOOKS['add_javascript_anonymous_page']['brandkitslater'] = $jsFiles;

    // Hook para injeção de elementos adicionais no formulário de login
    $PLUGIN_HOOKS['display_login']['brandkitslater'] = 'plugin_brandkitslater_display_login';
}

/**
 * Informações de versão e metadados do Plugin
 */
function plugin_version_brandkitslater() {
    return [
        'name'           => 'BrandKitSlater (Community Edition)',
        'version'        => PLUGIN_BRANDKITSLATER_VERSION,
        'author'         => 'Petryck Slater',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/petryckslater/glpi-plugin-brandkitslater-lite',
        'minGlpiVersion' => PLUGIN_BRANDKITSLATER_MIN_GLPI,
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_BRANDKITSLATER_MIN_GLPI,
            ]
        ]
    ];
}

/**
 * Checagem de pré-requisitos antes da instalação
 */
function plugin_brandkitslater_check_prerequisites() {
    return true;
}

/**
 * Checagem de configuração do plugin
 */
function plugin_brandkitslater_check_config($verbose = false) {
    return true;
}
