<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater Lite (Community Edition) para GLPI 10 e 11
 * Autor: Petryck Slater
 * Finalidade: Estúdio Open-Source de Identidade Visual, Logos (Menu Inteiro & Recolhido),
 *             Layouts de Login Glassmorphism e Temas Nativos para GLPI
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

define('PLUGIN_BRANDKITSLATER_VERSION', '2.2.0-lite');
define('PLUGIN_BRANDKITSLATER_MIN_GLPI', '10.0.0');

function plugin_init_brandkitslater() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['brandkitslater'] = true;
    $PLUGIN_HOOKS['config_page']['brandkitslater'] = 'front/config.form.php';

    if (class_exists('Glpi\Http\Firewall') && method_exists('Glpi\Http\Firewall', 'addPluginStrategyForLegacyScripts')) {
        \Glpi\Http\Firewall::addPluginStrategyForLegacyScripts(
            'brandkitslater',
            '#^/front/.*\.php$#',
            \Glpi\Http\Firewall::STRATEGY_AUTHENTICATED
        );
    }

    if (file_exists(__DIR__ . '/inc/config.class.php')) {
        require_once __DIR__ . '/inc/config.class.php';

        if (class_exists('Plugin') && method_exists('Plugin', 'registerClass')) {
            Plugin::registerClass('PluginBrandkitslaterConfig');
        }

        if (class_exists('Session') && Session::haveRight('config', READ)) {
            $PLUGIN_HOOKS['menu_toadd']['brandkitslater'] = ['config' => 'PluginBrandkitslaterConfig'];
        }
    }

    $PLUGIN_HOOKS['add_css_anonymous_page']['brandkitslater'] = 'css/brandkit_login.css';
    $PLUGIN_HOOKS['add_css']['brandkitslater'] = 'css/brandkit_theme.css';

    $jsFiles = ['js/brandkit_data.js', 'js/brandkit_icons.js'];
    $PLUGIN_HOOKS['add_javascript']['brandkitslater'] = $jsFiles;
    $PLUGIN_HOOKS['add_javascript_anonymous_page']['brandkitslater'] = $jsFiles;

    $PLUGIN_HOOKS['display_login']['brandkitslater'] = 'plugin_brandkitslater_display_login';
}

function plugin_version_brandkitslater() {
    return [
        'name'           => 'BrandKitSlater (Community Lite)',
        'version'        => PLUGIN_BRANDKITSLATER_VERSION,
        'author'         => 'Petryck Slater',
        'license'        => 'GPL-2.0',
        'homepage'       => 'https://github.com/PetryckSlater/glpi-plugin-brandkitslater-lite',
        'minGlpiVersion' => PLUGIN_BRANDKITSLATER_MIN_GLPI,
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_BRANDKITSLATER_MIN_GLPI,
            ]
        ]
    ];
}

function plugin_brandkitslater_check_prerequisites() {
    return true;
}

function plugin_brandkitslater_check_config($verbose = false) {
    return true;
}
