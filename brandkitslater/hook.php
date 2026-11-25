<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater (Community Edition) - Hooks de Instalação e Ciclo de Vida
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

/**
 * Instalação do Plugin e criação da tabela de personalização
 */
function plugin_brandkitslater_install() {
    global $DB;

    try {
        $table = 'glpi_plugin_brandkitslater_configs';

        if (!$DB->tableExists($table)) {
            $query = "CREATE TABLE IF NOT EXISTS `$table` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `preset` VARCHAR(50) NOT NULL DEFAULT 'slate',
                `login_position` VARCHAR(20) NOT NULL DEFAULT 'right',
                `card_style` VARCHAR(20) NOT NULL DEFAULT 'glassmorphism',
                `card_width` INT(11) NOT NULL DEFAULT 440,
                `card_radius` INT(11) NOT NULL DEFAULT 22,
                `card_blur` INT(11) NOT NULL DEFAULT 24,
                `card_opacity` INT(11) NOT NULL DEFAULT 94,
                `bg_image_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/login_bg.jpg',
                `bg_overlay_opacity` INT(11) NOT NULL DEFAULT 75,
                `primary_color` VARCHAR(20) NOT NULL DEFAULT '#0f172a',
                `primary_hover` VARCHAR(20) NOT NULL DEFAULT '#1e293b',
                `accent_color` VARCHAR(20) NOT NULL DEFAULT '#3b82f6',
                `navbar_bg` VARCHAR(20) NOT NULL DEFAULT '#0f172a',
                `font_family` VARCHAR(50) NOT NULL DEFAULT 'Plus Jakarta Sans',
                `icon_style` VARCHAR(20) NOT NULL DEFAULT 'tabler',
                `favicon_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/favicon.svg',
                `logo_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo.svg',
                `sidebar_logo_width` INT(11) NOT NULL DEFAULT 140,
                `sidebar_logo_height` INT(11) NOT NULL DEFAULT 36,
                `login_logo_width` INT(11) NOT NULL DEFAULT 220,
                `login_logo_height` INT(11) NOT NULL DEFAULT 60,
                `logo_shape` VARCHAR(30) NOT NULL DEFAULT 'standard',
                `logo_fit` VARCHAR(30) NOT NULL DEFAULT 'contain',
                `navbar_text_color` VARCHAR(20) NOT NULL DEFAULT '#ffffff',
                `navbar_border_color` VARCHAR(20) NOT NULL DEFAULT '#3b82f6',
                `sidebar_bg` VARCHAR(20) NOT NULL DEFAULT '#0f172a',
                `sidebar_text_color` VARCHAR(20) NOT NULL DEFAULT '#94a3b8',
                `sidebar_active_color` VARCHAR(20) NOT NULL DEFAULT '#38bdf8',
                `sidebar_active_bg` VARCHAR(30) NOT NULL DEFAULT 'rgba(56,189,248,0.12)',
                `body_bg` VARCHAR(20) NOT NULL DEFAULT '#f8fafc',
                `card_bg` VARCHAR(20) NOT NULL DEFAULT '#ffffff',
                `enable_external_register` TINYINT(1) NOT NULL DEFAULT 0,
                `external_register_url` VARCHAR(255) NOT NULL DEFAULT '',
                `enable_internal_theme` TINYINT(1) NOT NULL DEFAULT 0,
                `footer_text` VARCHAR(255) NOT NULL DEFAULT 'Central de Serviços & Atendimento • Powered by GLPI & BrandKit',
                `custom_css` TEXT DEFAULT NULL,
                `date_mod` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;";

            $DB->doQuery($query);
        } else {
            $newCols = [
                'enable_internal_theme' => "TINYINT(1) NOT NULL DEFAULT 0",
                'sidebar_logo_width'    => "INT(11) NOT NULL DEFAULT 140",
                'sidebar_logo_height'   => "INT(11) NOT NULL DEFAULT 36",
                'login_logo_width'      => "INT(11) NOT NULL DEFAULT 200",
                'login_logo_height'     => "INT(11) NOT NULL DEFAULT 55",
                'logo_shape'            => "VARCHAR(30) NOT NULL DEFAULT 'standard'",
                'logo_fit'              => "VARCHAR(30) NOT NULL DEFAULT 'contain'",
                'navbar_text_color'     => "VARCHAR(20) NOT NULL DEFAULT '#ffffff'",
                'navbar_border_color'   => "VARCHAR(20) NOT NULL DEFAULT '#3b82f6'",
                'sidebar_bg'            => "VARCHAR(20) NOT NULL DEFAULT '#0f172a'",
                'sidebar_text_color'    => "VARCHAR(20) NOT NULL DEFAULT '#94a3b8'",
                'sidebar_active_color'  => "VARCHAR(20) NOT NULL DEFAULT '#38bdf8'",
                'sidebar_active_bg'     => "VARCHAR(30) NOT NULL DEFAULT 'rgba(56,189,248,0.12)'",
                'body_bg'               => "VARCHAR(20) NOT NULL DEFAULT '#f8fafc'",
                'card_bg'               => "VARCHAR(20) NOT NULL DEFAULT '#ffffff'"
            ];

            foreach ($newCols as $colName => $colDef) {
                if (!$DB->fieldExists($table, $colName)) {
                    $DB->doQuery("ALTER TABLE `$table` ADD COLUMN `$colName` $colDef;");
                }
            }
        }

        // Garante que existe o registro padrão com id = 1
        $iterator = $DB->request([
            'FROM'  => $table,
            'WHERE' => ['id' => 1]
        ]);

        if (count($iterator) === 0) {
            $DB->insert($table, [
                'id'                       => 1,
                'preset'                   => 'slate',
                'login_position'           => 'right',
                'card_style'               => 'glassmorphism',
                'card_width'               => 440,
                'card_radius'              => 22,
                'card_blur'                => 24,
                'card_opacity'             => 94,
                'bg_image_url'             => '/plugins/brandkitslater/pics/login_bg.jpg',
                'bg_overlay_opacity'       => 75,
                'primary_color'            => '#0f172a',
                'primary_hover'            => '#1e293b',
                'accent_color'             => '#3b82f6',
                'navbar_bg'                => '#0f172a',
                'navbar_text_color'        => '#ffffff',
                'navbar_border_color'      => '#3b82f6',
                'sidebar_bg'               => '#0f172a',
                'sidebar_text_color'       => '#94a3b8',
                'sidebar_active_color'     => '#38bdf8',
                'sidebar_active_bg'        => 'rgba(56,189,248,0.12)',
                'body_bg'                  => '#f8fafc',
                'card_bg'                  => '#ffffff',
                'font_family'              => 'Plus Jakarta Sans',
                'icon_style'               => 'tabler',
                'favicon_url'              => '/plugins/brandkitslater/pics/favicon.svg',
                'logo_url'                 => '/plugins/brandkitslater/pics/logo.svg',
                'sidebar_logo_width'       => 140,
                'sidebar_logo_height'      => 36,
                'login_logo_width'         => 220,
                'login_logo_height'        => 60,
                'logo_shape'               => 'standard',
                'logo_fit'                 => 'contain',
                'enable_external_register' => 0,
                'external_register_url'    => '',
                'enable_internal_theme'    => 0,
                'footer_text'              => 'Central de Serviços & Atendimento • Powered by GLPI & BrandKit',
                'custom_css'               => ''
            ]);
        }

        // Gera os arquivos CSS estáticos iniciais
        $cssDir = __DIR__ . '/css';
        if (is_dir($cssDir)) {
            require_once __DIR__ . '/inc/cssgenerator.class.php';
            $defaults = [
                'preset'                   => 'slate',
                'login_position'           => 'right',
                'card_style'               => 'glassmorphism',
                'card_width'               => 440,
                'card_radius'              => 22,
                'card_blur'                => 24,
                'card_opacity'             => 94,
                'bg_image_url'             => '/plugins/brandkitslater/pics/login_bg.jpg',
                'bg_overlay_opacity'       => 75,
                'primary_color'            => '#0f172a',
                'primary_hover'            => '#1e293b',
                'accent_color'             => '#3b82f6',
                'navbar_bg'                => '#0f172a',
                'navbar_text_color'        => '#ffffff',
                'navbar_border_color'      => '#3b82f6',
                'sidebar_bg'               => '#0f172a',
                'sidebar_text_color'       => '#94a3b8',
                'sidebar_active_color'     => '#38bdf8',
                'sidebar_active_bg'        => 'rgba(56,189,248,0.12)',
                'body_bg'                  => '#f8fafc',
                'card_bg'                  => '#ffffff',
                'font_family'              => 'Plus Jakarta Sans',
                'icon_style'               => 'tabler',
                'favicon_url'              => '/plugins/brandkitslater/pics/favicon.svg',
                'logo_url'                 => '/plugins/brandkitslater/pics/logo.svg',
                'sidebar_logo_width'       => 140,
                'sidebar_logo_height'      => 36,
                'login_logo_width'         => 220,
                'login_logo_height'        => 60,
                'logo_shape'               => 'standard',
                'logo_fit'                 => 'contain',
                'enable_external_register' => 0,
                'external_register_url'    => '',
                'enable_internal_theme'    => 0,
                'footer_text'              => 'Central de Serviços & Atendimento • Powered by GLPI & BrandKit',
                'custom_css'               => ''
            ];
            @file_put_contents($cssDir . '/brandkit_login.css', PluginBrandkitslaterCssGenerator::generateLoginCss($defaults));
            @file_put_contents($cssDir . '/brandkit_theme.css', PluginBrandkitslaterCssGenerator::generateThemeCss($defaults));
        }

        return true;
    } catch (\Throwable $e) {
        if (class_exists('Toolbox')) {
            Toolbox::logInFile('php-errors', "BrandKitSlater Install Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        return false;
    }
}

/**
 * Desinstalação do Plugin
 */
function plugin_brandkitslater_uninstall() {
    return true;
}

/**
 * Hook disparado dentro do formulário de login do GLPI
 */
function plugin_brandkitslater_display_login() {
    global $DB;

    $table = 'glpi_plugin_brandkitslater_configs';
    if (!$DB->tableExists($table)) {
        return;
    }

    $iterator = $DB->request([
        'FROM'  => $table,
        'WHERE' => ['id' => 1]
    ]);

    $config = null;
    foreach ($iterator as $row) {
        $config = $row;
        break;
    }

    if (!$config || empty($config['enable_external_register']) || empty($config['external_register_url'])) {
        return;
    }

    $url = htmlspecialchars($config['external_register_url'], ENT_QUOTES, 'UTF-8');

    echo '
    <div class="brandkit-login-register-wrapper" style="margin-top: 1.25rem; text-align: center;">
        <div style="display: flex; align-items: center; margin-bottom: 0.9rem;">
            <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.12);"></div>
            <span style="padding: 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700;">Novo por aqui?</span>
            <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.12);"></div>
        </div>
        <a href="' . $url . '" target="_blank" class="brandkit-register-btn" style="
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        ">
            <svg style="width: 18px; height: 18px; margin-right: 8px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>Auto-Cadastro / Criar Conta</span>
        </a>
    </div>';
}
