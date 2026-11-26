<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater Lite - Hooks de Instalação, Migração e Renderização
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

function plugin_brandkitslater_install() {
    global $DB;

    try {
        require_once __DIR__ . '/inc/config.class.php';
        $table = PluginBrandkitslaterConfig::TABLE;
        $defaults = PluginBrandkitslaterConfig::getDefaults();

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
                `primary_color` VARCHAR(20) NOT NULL DEFAULT '#1e293b',
                `primary_hover` VARCHAR(20) NOT NULL DEFAULT '#334155',
                `accent_color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
                `navbar_bg` VARCHAR(20) NOT NULL DEFAULT '#0f172a',
                `navbar_text_color` VARCHAR(20) NOT NULL DEFAULT '#f8fafc',
                `navbar_border_color` VARCHAR(20) NOT NULL DEFAULT '#6366f1',
                `sidebar_bg` VARCHAR(20) NOT NULL DEFAULT '#0f172a',
                `sidebar_text_color` VARCHAR(20) NOT NULL DEFAULT '#cbd5e1',
                `sidebar_active_color` VARCHAR(20) NOT NULL DEFAULT '#38bdf8',
                `sidebar_active_bg` VARCHAR(40) NOT NULL DEFAULT 'rgba(99,102,241,0.16)',
                `sidebar_width` INT(11) NOT NULL DEFAULT 240,
                `sidebar_item_style` VARCHAR(25) NOT NULL DEFAULT 'pill',
                `body_bg` VARCHAR(20) NOT NULL DEFAULT '#f8fafc',
                `card_bg` VARCHAR(20) NOT NULL DEFAULT '#ffffff',
                `ui_border_radius` INT(11) NOT NULL DEFAULT 10,
                `internal_card_style` VARCHAR(25) NOT NULL DEFAULT 'elevated',
                `style_scrollbars_selection` TINYINT(1) NOT NULL DEFAULT 1,
                `font_family` VARCHAR(50) NOT NULL DEFAULT 'Plus Jakarta Sans',
                `icon_style` VARCHAR(20) NOT NULL DEFAULT 'tabler',
                `enable_custom_logos` TINYINT(1) NOT NULL DEFAULT 1,
                `favicon_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/favicon.ico',
                `logo_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo.svg',
                `sidebar_logo_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo_sidebar_light.svg',
                `sidebar_logo_width` INT(11) NOT NULL DEFAULT 150,
                `sidebar_logo_height` INT(11) NOT NULL DEFAULT 40,
                `sidebar_logo_shape` VARCHAR(30) NOT NULL DEFAULT 'standard',
                `sidebar_logo_bg_pill` TINYINT(1) NOT NULL DEFAULT 0,
                `collapsed_logo_url` VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo_collapsed.svg',
                `collapsed_logo_size` INT(11) NOT NULL DEFAULT 38,
                `collapsed_logo_shape` VARCHAR(30) NOT NULL DEFAULT 'rounded',
                `login_logo_width` INT(11) NOT NULL DEFAULT 210,
                `login_logo_height` INT(11) NOT NULL DEFAULT 58,
                `logo_shape` VARCHAR(30) NOT NULL DEFAULT 'standard',
                `logo_fit` VARCHAR(30) NOT NULL DEFAULT 'contain',
                `login_badge_text` VARCHAR(120) NOT NULL DEFAULT 'Central de Atendimento & Suporte',
                `login_title` VARCHAR(120) NOT NULL DEFAULT 'Entrar no Sistema',
                `login_subtitle` VARCHAR(180) NOT NULL DEFAULT 'Acesso corporativo seguro',
                `login_btn_text` VARCHAR(80) NOT NULL DEFAULT 'Acessar Central',
                `enable_external_register` TINYINT(1) NOT NULL DEFAULT 0,
                `external_register_url` VARCHAR(255) NOT NULL DEFAULT '/cadastro/',
                `external_register_text` VARCHAR(120) NOT NULL DEFAULT 'Auto-Cadastro de Usuários Externos',
                `page_title_brand` VARCHAR(80) NOT NULL DEFAULT '',
                `enable_internal_theme` TINYINT(1) NOT NULL DEFAULT 0,
                `footer_text` VARCHAR(255) NOT NULL DEFAULT 'Ambiente Seguro com Criptografia TLS • Central de Serviços',
                `custom_css` TEXT DEFAULT NULL,
                `date_mod` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;";

            $DB->doQuery($query);
        } else {
            foreach (PluginBrandkitslaterConfig::getMigrationColumns() as $colName => $colDef) {
                if (!$DB->fieldExists($table, $colName)) {
                    $DB->doQuery("ALTER TABLE `$table` ADD COLUMN `$colName` $colDef;");
                }
            }
        }

        $iterator = $DB->request([
            'FROM'  => $table,
            'WHERE' => ['id' => 1]
        ]);

        if (count($iterator) === 0) {
            $DB->insert($table, $defaults);
        }

        $activeConfig = PluginBrandkitslaterConfig::getConfig();
        PluginBrandkitslaterConfig::compileAssets($activeConfig);

        return true;
    } catch (\Throwable $e) {
        if (class_exists('Toolbox')) {
            Toolbox::logInFile('php-errors', "BrandKitSlater Lite Install Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        return false;
    }
}

function plugin_brandkitslater_uninstall() {
    return true;
}

function plugin_brandkitslater_display_login() {
    global $DB;

    $table = 'glpi_plugin_brandkitslater_configs';
    if (!isset($DB) || !$DB || !$DB->tableExists($table)) {
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

    if (!$config || empty($config['enable_external_register'])) {
        return;
    }

    $url  = htmlspecialchars($config['external_register_url'] ?? '/cadastro/', ENT_QUOTES, 'UTF-8');
    $text = htmlspecialchars(!empty($config['external_register_text']) ? $config['external_register_text'] : 'Auto-Cadastro de Usuários Externos', ENT_QUOTES, 'UTF-8');
    $primary = htmlspecialchars($config['primary_color'] ?? '#1e293b', ENT_QUOTES, 'UTF-8');

    echo '
    <div class="brandkit-login-register-wrapper" style="margin-top: 1.25rem; text-align: center;">
        <div style="display: flex; align-items: center; margin-bottom: 0.9rem;">
            <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.12);"></div>
            <span style="padding: 0 10px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700;">Novo por aqui?</span>
            <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.12);"></div>
        </div>
        <a href="' . $url . '" target="_blank" rel="noopener" class="brandkit-register-btn" style="
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
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        " onmouseover="this.style.borderColor=\'' . $primary . '\'; this.style.color=\'' . $primary . '\'; this.style.transform=\'translateY(-1px)\';" onmouseout="this.style.borderColor=\'#cbd5e1\'; this.style.color=\'#1e293b\'; this.style.transform=\'none\';">
            <svg style="width: 18px; height: 18px; margin-right: 8px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span>' . $text . '</span>
        </a>
    </div>';
}
