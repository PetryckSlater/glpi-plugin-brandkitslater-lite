<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater Lite - Classe de Configurações, Menu Nativo e Persistência
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

if (!class_exists('PluginBrandkitslaterBase')) {
    if (class_exists('CommonGLPI')) {
        class PluginBrandkitslaterBase extends CommonGLPI {}
    } else {
        class PluginBrandkitslaterBase {}
    }
}

class PluginBrandkitslaterConfig extends PluginBrandkitslaterBase {
    static $rightname = 'config';
    public const TABLE = 'glpi_plugin_brandkitslater_configs';

    public static function getTypeName($nb = 0): string {
        return 'BrandKit Studio';
    }

    public static function getMenuName(): string {
        return 'BrandKit Studio';
    }

    public static function getIcon(): string {
        return 'ti ti-palette';
    }

    public static function getMenuContent(): array|false {
        if (!self::canView()) {
            return false;
        }

        $webDir = (class_exists('Plugin') && method_exists('Plugin', 'getWebDir'))
            ? Plugin::getWebDir('brandkitslater')
            : '/plugins/brandkitslater';

        return [
            'title' => self::getMenuName(),
            'page'  => $webDir . '/front/config.form.php',
            'icon'  => self::getIcon(),
        ];
    }

    public static function canCreate(): bool {
        return class_exists('Session') ? Session::haveRight(self::$rightname, UPDATE) : true;
    }

    public static function canView(): bool {
        return class_exists('Session') ? Session::haveRight(self::$rightname, READ) : true;
    }

    public static function canUpdate(): bool {
        return class_exists('Session') ? Session::haveRight(self::$rightname, UPDATE) : true;
    }

    public static function canDelete(): bool {
        return class_exists('Session') ? Session::haveRight(self::$rightname, UPDATE) : true;
    }

    public static function canPurge(): bool {
        return class_exists('Session') ? Session::haveRight(self::$rightname, UPDATE) : true;
    }

    public static function getDefaults(): array {
        return [
            'id'                        => 1,
            'preset'                    => 'slate',
            'login_position'            => 'right',
            'card_style'                => 'glassmorphism',
            'card_width'                => 440,
            'card_radius'               => 22,
            'card_blur'                 => 24,
            'card_opacity'              => 94,
            'bg_image_url'              => '/plugins/brandkitslater/pics/login_bg.jpg',
            'bg_overlay_opacity'        => 75,
            'primary_color'             => '#1e293b',
            'primary_hover'             => '#334155',
            'accent_color'              => '#6366f1',
            'navbar_bg'                 => '#0f172a',
            'navbar_text_color'         => '#f8fafc',
            'navbar_border_color'       => '#6366f1',
            'sidebar_bg'                => '#0f172a',
            'sidebar_text_color'        => '#cbd5e1',
            'sidebar_active_color'      => '#38bdf8',
            'sidebar_active_bg'         => 'rgba(99,102,241,0.16)',
            'sidebar_width'             => 240,
            'sidebar_item_style'        => 'pill',
            'body_bg'                   => '#f8fafc',
            'card_bg'                   => '#ffffff',
            'ui_border_radius'          => 10,
            'internal_card_style'       => 'elevated',
            'style_scrollbars_selection'=> 1,
            'font_family'               => 'Plus Jakarta Sans',
            'icon_style'                => 'tabler',
            'enable_custom_logos'       => 1,
            'favicon_url'               => '/plugins/brandkitslater/pics/favicon.ico',
            'logo_url'                  => '/plugins/brandkitslater/pics/logo.svg',
            'sidebar_logo_url'          => '/plugins/brandkitslater/pics/logo_sidebar_light.svg',
            'sidebar_logo_width'        => 150,
            'sidebar_logo_height'       => 40,
            'sidebar_logo_shape'        => 'standard',
            'sidebar_logo_bg_pill'      => 0,
            'collapsed_logo_url'        => '/plugins/brandkitslater/pics/logo_collapsed.svg',
            'collapsed_logo_size'       => 38,
            'collapsed_logo_shape'      => 'rounded',
            'login_logo_width'          => 210,
            'login_logo_height'         => 58,
            'logo_shape'                => 'standard',
            'logo_fit'                  => 'contain',
            'login_badge_text'          => 'Central de Atendimento & Suporte',
            'login_title'               => 'Entrar no Sistema',
            'login_subtitle'            => 'Acesso corporativo seguro',
            'login_btn_text'            => 'Acessar Central',
            'enable_external_register'  => 0,
            'external_register_url'     => '/cadastro/',
            'external_register_text'    => 'Auto-Cadastro de Usuários Externos',
            'page_title_brand'          => '',
            'enable_internal_theme'     => 0,
            'footer_text'               => 'Ambiente Seguro com Criptografia TLS • Central de Serviços',
            'custom_css'                => ''
        ];
    }

    public static function getMigrationColumns(): array {
        return [
            'enable_internal_theme'      => "TINYINT(1) NOT NULL DEFAULT 0",
            'enable_custom_logos'        => "TINYINT(1) NOT NULL DEFAULT 1",
            'sidebar_logo_url'           => "VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo_sidebar_light.svg'",
            'sidebar_logo_width'         => "INT(11) NOT NULL DEFAULT 150",
            'sidebar_logo_height'        => "INT(11) NOT NULL DEFAULT 40",
            'sidebar_logo_shape'         => "VARCHAR(30) NOT NULL DEFAULT 'standard'",
            'sidebar_logo_bg_pill'       => "TINYINT(1) NOT NULL DEFAULT 0",
            'collapsed_logo_url'         => "VARCHAR(255) NOT NULL DEFAULT '/plugins/brandkitslater/pics/logo_collapsed.svg'",
            'collapsed_logo_size'        => "INT(11) NOT NULL DEFAULT 38",
            'collapsed_logo_shape'       => "VARCHAR(30) NOT NULL DEFAULT 'rounded'",
            'login_logo_width'           => "INT(11) NOT NULL DEFAULT 210",
            'login_logo_height'          => "INT(11) NOT NULL DEFAULT 58",
            'logo_shape'                 => "VARCHAR(30) NOT NULL DEFAULT 'standard'",
            'logo_fit'                   => "VARCHAR(30) NOT NULL DEFAULT 'contain'",
            'navbar_text_color'          => "VARCHAR(20) NOT NULL DEFAULT '#f8fafc'",
            'navbar_border_color'        => "VARCHAR(20) NOT NULL DEFAULT '#6366f1'",
            'sidebar_bg'                 => "VARCHAR(20) NOT NULL DEFAULT '#0f172a'",
            'sidebar_text_color'         => "VARCHAR(20) NOT NULL DEFAULT '#cbd5e1'",
            'sidebar_active_color'       => "VARCHAR(20) NOT NULL DEFAULT '#38bdf8'",
            'sidebar_active_bg'          => "VARCHAR(40) NOT NULL DEFAULT 'rgba(99,102,241,0.16)'",
            'sidebar_width'              => "INT(11) NOT NULL DEFAULT 240",
            'sidebar_item_style'         => "VARCHAR(25) NOT NULL DEFAULT 'pill'",
            'body_bg'                    => "VARCHAR(20) NOT NULL DEFAULT '#f8fafc'",
            'card_bg'                    => "VARCHAR(20) NOT NULL DEFAULT '#ffffff'",
            'ui_border_radius'           => "INT(11) NOT NULL DEFAULT 10",
            'internal_card_style'        => "VARCHAR(25) NOT NULL DEFAULT 'elevated'",
            'style_scrollbars_selection' => "TINYINT(1) NOT NULL DEFAULT 1",
            'login_badge_text'           => "VARCHAR(120) NOT NULL DEFAULT 'Central de Atendimento & Suporte'",
            'login_title'                => "VARCHAR(120) NOT NULL DEFAULT 'Entrar no Sistema'",
            'login_subtitle'             => "VARCHAR(180) NOT NULL DEFAULT 'Acesso corporativo seguro'",
            'login_btn_text'             => "VARCHAR(80) NOT NULL DEFAULT 'Acessar Central'",
            'external_register_text'     => "VARCHAR(120) NOT NULL DEFAULT 'Auto-Cadastro de Usuários Externos'",
            'page_title_brand'           => "VARCHAR(80) NOT NULL DEFAULT ''"
        ];
    }

    public static function ensureSchema(): void {
        global $DB;
        if (!isset($DB) || !$DB) {
            return;
        }

        $table = self::TABLE;
        if (!$DB->tableExists($table)) {
            require_once dirname(__DIR__) . '/hook.php';
            if (function_exists('plugin_brandkitslater_install')) {
                plugin_brandkitslater_install();
            }
            return;
        }

        foreach (self::getMigrationColumns() as $colName => $colDef) {
            if (!$DB->fieldExists($table, $colName)) {
                $DB->doQuery("ALTER TABLE `$table` ADD COLUMN `$colName` $colDef;");
            }
        }
    }

    public static function getConfig(): array {
        global $DB;

        $defaults = self::getDefaults();
        if (!isset($DB) || !$DB) {
            return $defaults;
        }

        self::ensureSchema();
        $table = self::TABLE;

        if (!$DB->tableExists($table)) {
            return $defaults;
        }

        $iterator = $DB->request([
            'FROM'  => $table,
            'WHERE' => ['id' => 1]
        ]);

        foreach ($iterator as $row) {
            $merged = array_merge($defaults, $row);
            if (empty(trim($merged['logo_url'] ?? ''))) {
                $merged['logo_url'] = $defaults['logo_url'];
            }
            if (empty(trim($merged['favicon_url'] ?? ''))) {
                $merged['favicon_url'] = $defaults['favicon_url'];
            }
            if (empty(trim($merged['collapsed_logo_url'] ?? ''))) {
                $merged['collapsed_logo_url'] = $defaults['collapsed_logo_url'];
            }
            return $merged;
        }

        return $defaults;
    }

    public static function compileAssets(array $fields): void {
        $baseDir = dirname(__DIR__);
        $cssDir  = $baseDir . '/css';
        $jsDir   = $baseDir . '/js';

        if (is_dir($cssDir)) {
            require_once __DIR__ . '/cssgenerator.class.php';
            @file_put_contents($cssDir . '/brandkit_login.css', PluginBrandkitslaterCssGenerator::generateLoginCss($fields));
            @file_put_contents($cssDir . '/brandkit_theme.css', PluginBrandkitslaterCssGenerator::generateThemeCss($fields));
        }

        if (is_dir($jsDir)) {
            $mainLogo = !empty($fields['logo_url']) ? $fields['logo_url'] : '/plugins/brandkitslater/pics/logo.svg';
            $sideLogo = !empty(trim($fields['sidebar_logo_url'] ?? '')) ? trim($fields['sidebar_logo_url']) : $mainLogo;
            $collLogo = !empty(trim($fields['collapsed_logo_url'] ?? '')) ? trim($fields['collapsed_logo_url']) : '/plugins/brandkitslater/pics/logo_collapsed.svg';
            $favUrl   = !empty($fields['favicon_url']) ? $fields['favicon_url'] : '/plugins/brandkitslater/pics/favicon.ico';

            $jsonData = json_encode([
                'enable_custom_logos'   => (int)($fields['enable_custom_logos'] ?? 1),
                'logo_url'              => $mainLogo,
                'sidebar_logo_url'      => $sideLogo,
                'collapsed_logo_url'    => $collLogo,
                'favicon_url'           => $favUrl,
                'sidebar_logo_width'    => (int)($fields['sidebar_logo_width'] ?? 150),
                'sidebar_logo_height'   => (int)($fields['sidebar_logo_height'] ?? 40),
                'sidebar_logo_shape'    => $fields['sidebar_logo_shape'] ?? 'standard',
                'sidebar_logo_bg_pill'  => (int)($fields['sidebar_logo_bg_pill'] ?? 0),
                'collapsed_logo_size'   => (int)($fields['collapsed_logo_size'] ?? 38),
                'collapsed_logo_shape'  => $fields['collapsed_logo_shape'] ?? 'rounded',
                'login_logo_width'      => (int)($fields['login_logo_width'] ?? 210),
                'login_logo_height'     => (int)($fields['login_logo_height'] ?? 58),
                'logo_shape'            => $fields['logo_shape'] ?? 'standard',
                'logo_fit'              => $fields['logo_fit'] ?? 'contain',
                'primary_color'         => $fields['primary_color'] ?? '#1e293b',
                'login_title'           => $fields['login_title'] ?? 'Entrar no Sistema',
                'login_subtitle'        => $fields['login_subtitle'] ?? 'Acesso corporativo seguro',
                'login_btn_text'        => $fields['login_btn_text'] ?? 'Acessar Central',
                'page_title_brand'      => $fields['page_title_brand'] ?? ''
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            @file_put_contents($jsDir . '/brandkit_data.js', "window.BRANDKIT_DATA = " . $jsonData . ";\n");
        }
    }

    private static function processMediaUpload(array $files, string $fileKey, string $baseName, array $allowedExts, string $currentUrl, string $picsDir): string {
        if (!empty($files[$fileKey]['tmp_name']) && ($files[$fileKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($files[$fileKey]['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExts, true)) {
                $fileName = $baseName . '.' . $ext;
                $target   = $picsDir . '/' . $fileName;
                if (@move_uploaded_file($files[$fileKey]['tmp_name'], $target)) {
                    @chmod($target, 0664);
                    return '/plugins/brandkitslater/pics/' . $fileName . '?v=' . time();
                }
            }
        }
        return $currentUrl;
    }

    public static function saveConfig(array $input, array $files = []): bool {
        global $DB;

        if (!self::canUpdate()) {
            return false;
        }

        self::ensureSchema();
        $table = self::TABLE;
        $defaults = self::getDefaults();

        $picsDir = dirname(__DIR__) . '/pics';
        if (!is_dir($picsDir)) {
            @mkdir($picsDir, 0775, true);
        }

        $bgImageUrl = self::processMediaUpload(
            $files, 'bg_image_file', 'custom_login_bg', ['jpg', 'jpeg', 'png', 'webp'],
            trim($input['bg_image_url'] ?? $defaults['bg_image_url']), $picsDir
        );

        $faviconUrl = self::processMediaUpload(
            $files, 'favicon_file', 'custom_favicon', ['ico', 'png', 'svg'],
            trim($input['favicon_url'] ?? $defaults['favicon_url']), $picsDir
        );

        $logoUrl = self::processMediaUpload(
            $files, 'logo_file', 'custom_logo', ['png', 'svg', 'jpg', 'jpeg', 'webp'],
            trim($input['logo_url'] ?? $defaults['logo_url']), $picsDir
        );

        $sidebarLogoUrl = self::processMediaUpload(
            $files, 'sidebar_logo_file', 'custom_sidebar_logo', ['png', 'svg', 'jpg', 'jpeg', 'webp'],
            trim($input['sidebar_logo_url'] ?? ''), $picsDir
        );

        $collapsedLogoUrl = self::processMediaUpload(
            $files, 'collapsed_logo_file', 'custom_collapsed_logo', ['png', 'svg', 'ico', 'jpg', 'jpeg', 'webp'],
            trim($input['collapsed_logo_url'] ?? $defaults['collapsed_logo_url']), $picsDir
        );

        $validShapes = ['standard', 'rounded', 'circle', 'pill', 'glass_badge', 'bordered', 'glow'];

        $fields = [
            'preset'                     => trim($input['preset'] ?? $defaults['preset']),
            'login_position'             => in_array($input['login_position'] ?? '', ['right', 'center', 'left'], true) ? $input['login_position'] : 'right',
            'card_style'                 => in_array($input['card_style'] ?? '', ['glassmorphism', 'flat', 'bordered'], true) ? $input['card_style'] : 'glassmorphism',
            'card_width'                 => max(360, min(650, (int)($input['card_width'] ?? 440))),
            'card_radius'                => max(0, min(36, (int)($input['card_radius'] ?? 22))),
            'card_blur'                  => max(0, min(40, (int)($input['card_blur'] ?? 24))),
            'card_opacity'               => max(50, min(100, (int)($input['card_opacity'] ?? 94))),
            'bg_image_url'               => $bgImageUrl,
            'bg_overlay_opacity'         => max(0, min(95, (int)($input['bg_overlay_opacity'] ?? 75))),
            'primary_color'              => trim($input['primary_color'] ?? $defaults['primary_color']),
            'primary_hover'              => trim($input['primary_hover'] ?? $defaults['primary_hover']),
            'accent_color'               => trim($input['accent_color'] ?? $defaults['accent_color']),
            'navbar_bg'                  => trim($input['navbar_bg'] ?? $defaults['navbar_bg']),
            'navbar_text_color'          => trim($input['navbar_text_color'] ?? $defaults['navbar_text_color']),
            'navbar_border_color'        => trim($input['navbar_border_color'] ?? $defaults['navbar_border_color']),
            'sidebar_bg'                 => trim($input['sidebar_bg'] ?? $defaults['sidebar_bg']),
            'sidebar_text_color'         => trim($input['sidebar_text_color'] ?? $defaults['sidebar_text_color']),
            'sidebar_active_color'       => trim($input['sidebar_active_color'] ?? $defaults['sidebar_active_color']),
            'sidebar_active_bg'          => trim($input['sidebar_active_bg'] ?? $defaults['sidebar_active_bg']),
            'sidebar_width'              => max(200, min(300, (int)($input['sidebar_width'] ?? 240))),
            'sidebar_item_style'         => in_array($input['sidebar_item_style'] ?? '', ['native', 'pill', 'glass'], true) ? $input['sidebar_item_style'] : 'pill',
            'body_bg'                    => trim($input['body_bg'] ?? $defaults['body_bg']),
            'card_bg'                    => trim($input['card_bg'] ?? $defaults['card_bg']),
            'ui_border_radius'           => max(0, min(20, (int)($input['ui_border_radius'] ?? 10))),
            'internal_card_style'        => in_array($input['internal_card_style'] ?? '', ['native', 'elevated', 'accent_top'], true) ? $input['internal_card_style'] : 'elevated',
            'style_scrollbars_selection' => !empty($input['style_scrollbars_selection']) ? 1 : 0,
            'font_family'                => trim($input['font_family'] ?? 'Plus Jakarta Sans'),
            'icon_style'                 => trim($input['icon_style'] ?? 'tabler'),
            'enable_custom_logos'        => !empty($input['enable_custom_logos']) ? 1 : 0,
            'favicon_url'                => $faviconUrl,
            'logo_url'                   => $logoUrl,
            'sidebar_logo_url'           => $sidebarLogoUrl,
            'sidebar_logo_width'         => max(50, min(260, (int)($input['sidebar_logo_width'] ?? 150))),
            'sidebar_logo_height'        => max(20, min(90, (int)($input['sidebar_logo_height'] ?? 40))),
            'sidebar_logo_shape'         => in_array($input['sidebar_logo_shape'] ?? '', $validShapes, true) ? $input['sidebar_logo_shape'] : 'standard',
            'sidebar_logo_bg_pill'       => !empty($input['sidebar_logo_bg_pill']) ? 1 : 0,
            'collapsed_logo_url'         => $collapsedLogoUrl,
            'collapsed_logo_size'        => max(22, min(56, (int)($input['collapsed_logo_size'] ?? 38))),
            'collapsed_logo_shape'       => in_array($input['collapsed_logo_shape'] ?? '', $validShapes, true) ? $input['collapsed_logo_shape'] : 'rounded',
            'login_logo_width'           => max(80, min(400, (int)($input['login_logo_width'] ?? 210))),
            'login_logo_height'          => max(25, min(150, (int)($input['login_logo_height'] ?? 58))),
            'logo_shape'                 => in_array($input['logo_shape'] ?? '', $validShapes, true) ? $input['logo_shape'] : 'standard',
            'logo_fit'                   => in_array($input['logo_fit'] ?? '', ['contain', 'cover', 'scale-down'], true) ? $input['logo_fit'] : 'contain',
            'login_badge_text'           => trim($input['login_badge_text'] ?? ''),
            'login_title'                => trim($input['login_title'] ?? $defaults['login_title']),
            'login_subtitle'             => trim($input['login_subtitle'] ?? $defaults['login_subtitle']),
            'login_btn_text'             => trim($input['login_btn_text'] ?? $defaults['login_btn_text']),
            'enable_external_register'   => !empty($input['enable_external_register']) ? 1 : 0,
            'external_register_url'      => trim($input['external_register_url'] ?? '/cadastro/'),
            'external_register_text'     => trim($input['external_register_text'] ?? $defaults['external_register_text']),
            'page_title_brand'           => trim($input['page_title_brand'] ?? ''),
            'enable_internal_theme'      => !empty($input['enable_internal_theme']) ? 1 : 0,
            'footer_text'                => trim($input['footer_text'] ?? ''),
            'custom_css'                 => trim($input['custom_css'] ?? '')
        ];

        self::compileAssets($fields);

        $check = $DB->request([
            'FROM'  => $table,
            'WHERE' => ['id' => 1]
        ]);
        if (count($check) === 0) {
            $fields['id'] = 1;
            return (bool)$DB->insert($table, $fields);
        }

        return (bool)$DB->update($table, $fields, ['id' => 1]);
    }
}
