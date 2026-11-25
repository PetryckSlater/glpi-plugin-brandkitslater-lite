<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Classe de Configurações e Persistência
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

class PluginBrandkitslaterConfig {
    static $rightname = 'config';
    public const TABLE = 'glpi_plugin_brandkitslater_configs';

    public static function getTypeName($nb = 0): string {
        return 'BrandKitSlater';
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

    /**
     * Garante que o esquema e colunas existam no banco de dados
     */
    public static function ensureSchema(): void {
        global $DB;
        $table = self::TABLE;
        if (!$DB->tableExists($table)) {
            require_once dirname(__DIR__) . '/hook.php';
            if (function_exists('plugin_brandkitslater_install')) {
                plugin_brandkitslater_install();
            }
            return;
        }

        $newCols = [
            'enable_internal_theme' => "TINYINT(1) NOT NULL DEFAULT 0",
            'sidebar_logo_width'    => "INT(11) NOT NULL DEFAULT 140",
            'sidebar_logo_height'   => "INT(11) NOT NULL DEFAULT 36",
            'login_logo_width'      => "INT(11) NOT NULL DEFAULT 200",
            'login_logo_height'     => "INT(11) NOT NULL DEFAULT 55",
            'logo_shape'            => "VARCHAR(30) NOT NULL DEFAULT 'standard'",
            'logo_fit'              => "VARCHAR(30) NOT NULL DEFAULT 'contain'",
            'navbar_text_color'     => "VARCHAR(20) NOT NULL DEFAULT '#ffffff'",
            'navbar_border_color'   => "VARCHAR(20) NOT NULL DEFAULT '#C0122D'",
            'sidebar_bg'            => "VARCHAR(20) NOT NULL DEFAULT '#0A1428'",
            'sidebar_text_color'    => "VARCHAR(20) NOT NULL DEFAULT '#cbd5e1'",
            'sidebar_active_color'  => "VARCHAR(20) NOT NULL DEFAULT '#38bdf8'",
            'sidebar_active_bg'     => "VARCHAR(30) NOT NULL DEFAULT 'rgba(255,255,255,0.08)'",
            'body_bg'               => "VARCHAR(20) NOT NULL DEFAULT '#f8fafc'",
            'card_bg'               => "VARCHAR(20) NOT NULL DEFAULT '#ffffff'"
        ];

        foreach ($newCols as $colName => $colDef) {
            if (!$DB->fieldExists($table, $colName)) {
                $DB->doQuery("ALTER TABLE `$table` ADD COLUMN `$colName` $colDef;");
            }
        }
    }

    /**
     * Retorna as configurações ativas com valores padrão seguros
     */
    public static function getConfig(): array {
        global $DB;

        self::ensureSchema();
        $table = self::TABLE;

        $defaults = [
            'id'                       => 1,
            'preset'                   => 'slate',
            'login_position'           => 'right', // right, center, left
            'card_style'               => 'glassmorphism', // glassmorphism, flat, bordered
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
            'logo_shape'               => 'standard', // standard, rounded, circle, pill, bordered, glow
            'logo_fit'                 => 'contain',  // contain, cover, scale-down
            'enable_external_register' => 0,
            'external_register_url'    => '',
            'enable_internal_theme'    => 0, // 0 = Mantém 100% o Light Mode original do GLPI
            'footer_text'              => 'Central de Serviços & Atendimento • Powered by GLPI & BrandKit',
            'custom_css'               => ''
        ];

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
                $merged['logo_url'] = '/plugins/brandkitslater/pics/logo.svg';
            }
            if (empty(trim($merged['favicon_url'] ?? ''))) {
                $merged['favicon_url'] = '/plugins/brandkitslater/pics/favicon.svg';
            }
            return $merged;
        }
                $merged['favicon_url'] = '/plugins/brandkitslater/pics/favicon.ico';
            }
            return $merged;
        }

        return $defaults;
    }

    /**
     * Atualiza as configurações e processa uploads de mídia
     */
    public static function saveConfig(array $input, array $files = []): bool {
        global $DB;

        if (!self::canUpdate()) {
            return false;
        }

        self::ensureSchema();
        $table = self::TABLE;

        $picsDir = dirname(__DIR__) . '/pics';
        if (!is_dir($picsDir)) {
            @mkdir($picsDir, 0775, true);
        }

        // 1. Processa upload da Imagem de Fundo (login_bg)
        $bgImageUrl = trim($input['bg_image_url'] ?? '');
        if (!empty($files['bg_image_file']['tmp_name']) && $files['bg_image_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($files['bg_image_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $target = $picsDir . '/login_bg.' . $ext;
                if (@move_uploaded_file($files['bg_image_file']['tmp_name'], $target)) {
                    @chmod($target, 0664);
                    $bgImageUrl = '/plugins/brandkitslater/pics/login_bg.' . $ext . '?v=' . time();
                }
            }
        }

        // 2. Processa upload do Favicon
        $faviconUrl = trim($input['favicon_url'] ?? '');
        if (!empty($files['favicon_file']['tmp_name']) && $files['favicon_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($files['favicon_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['ico', 'png', 'svg'])) {
                $target = $picsDir . '/favicon.' . $ext;
                if (@move_uploaded_file($files['favicon_file']['tmp_name'], $target)) {
                    @chmod($target, 0664);
                    $faviconUrl = '/plugins/brandkitslater/pics/favicon.' . $ext . '?v=' . time();

                    // Substitui também o favicon nativo do GLPI em /public/pics/ para navegadores sem JS
                    $glpiRoots = [
                        dirname(__DIR__, 2) . '/public/pics',
                        dirname(__DIR__, 3) . '/public/pics',
                        '/var/www/html/public/pics'
                    ];
                    foreach ($glpiRoots as $gDir) {
                        if (is_dir($gDir)) {
                            @copy($target, $gDir . '/favicon.ico');
                            @copy($target, $gDir . '/favicon.png');
                            @chmod($gDir . '/favicon.ico', 0664);
                        }
                    }
                }
            }
        }

        // 3. Processa upload do Logo Institucional
        $logoUrl = trim($input['logo_url'] ?? '');
        if (!empty($files['logo_file']['tmp_name']) && $files['logo_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($files['logo_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['png', 'svg', 'jpg', 'jpeg', 'webp'])) {
                $target = $picsDir . '/logo.' . $ext;
                if (@move_uploaded_file($files['logo_file']['tmp_name'], $target)) {
                    @chmod($target, 0664);
                    $logoUrl = '/plugins/brandkitslater/pics/logo.' . $ext . '?v=' . time();
                    // Garante também logo.png como fallback padrão
                    if ($ext !== 'png') {
                        @copy($target, $picsDir . '/logo.png');
                    }
                }
            }
        }

        $fields = [
            'preset'                   => $input['preset'] ?? 'slate',
            'login_position'           => in_array($input['login_position'] ?? '', ['right', 'center', 'left']) ? $input['login_position'] : 'right',
            'card_style'               => in_array($input['card_style'] ?? '', ['glassmorphism', 'flat', 'bordered']) ? $input['card_style'] : 'glassmorphism',
            'card_width'               => max(360, min(650, (int)($input['card_width'] ?? 440))),
            'card_radius'              => max(0, min(36, (int)($input['card_radius'] ?? 22))),
            'card_blur'                => max(0, min(40, (int)($input['card_blur'] ?? 24))),
            'card_opacity'             => max(50, min(100, (int)($input['card_opacity'] ?? 94))),
            'bg_image_url'             => $bgImageUrl,
            'bg_overlay_opacity'       => max(0, min(95, (int)($input['bg_overlay_opacity'] ?? 75))),
            'primary_color'            => trim($input['primary_color'] ?? '#003876'),
            'primary_hover'            => trim($input['primary_hover'] ?? '#004ba0'),
            'accent_color'             => trim($input['accent_color'] ?? '#C0122D'),
            'navbar_bg'                => trim($input['navbar_bg'] ?? '#0A1428'),
            'navbar_text_color'        => trim($input['navbar_text_color'] ?? '#ffffff'),
            'navbar_border_color'      => trim($input['navbar_border_color'] ?? '#C0122D'),
            'sidebar_bg'               => trim($input['sidebar_bg'] ?? '#0A1428'),
            'sidebar_text_color'       => trim($input['sidebar_text_color'] ?? '#cbd5e1'),
            'sidebar_active_color'     => trim($input['sidebar_active_color'] ?? '#38bdf8'),
            'sidebar_active_bg'        => trim($input['sidebar_active_bg'] ?? 'rgba(255,255,255,0.08)'),
            'body_bg'                  => trim($input['body_bg'] ?? '#f8fafc'),
            'card_bg'                  => trim($input['card_bg'] ?? '#ffffff'),
            'font_family'              => trim($input['font_family'] ?? 'Plus Jakarta Sans'),
            'icon_style'               => trim($input['icon_style'] ?? 'tabler'),
            'favicon_url'              => $faviconUrl,
            'logo_url'                 => $logoUrl,
            'sidebar_logo_width'       => max(50, min(300, (int)($input['sidebar_logo_width'] ?? 140))),
            'sidebar_logo_height'      => max(20, min(100, (int)($input['sidebar_logo_height'] ?? 36))),
            'login_logo_width'         => max(80, min(400, (int)($input['login_logo_width'] ?? 200))),
            'login_logo_height'        => max(25, min(150, (int)($input['login_logo_height'] ?? 55))),
            'logo_shape'               => in_array($input['logo_shape'] ?? '', ['standard', 'rounded', 'circle', 'pill', 'bordered', 'glow']) ? $input['logo_shape'] : 'standard',
            'logo_fit'                 => in_array($input['logo_fit'] ?? '', ['contain', 'cover', 'scale-down']) ? $input['logo_fit'] : 'contain',
            'enable_external_register' => !empty($input['enable_external_register']) ? 1 : 0,
            'external_register_url'    => trim($input['external_register_url'] ?? '/cadastro/'),
            'enable_internal_theme'    => !empty($input['enable_internal_theme']) ? 1 : 0,
            'footer_text'              => trim($input['footer_text'] ?? ''),
            'custom_css'               => trim($input['custom_css'] ?? '')
        ];

        // Regenera os arquivos de CSS estáticos para carregamento ultra-rápido no Nginx
        $cssDir = dirname(__DIR__) . '/css';
        if (is_dir($cssDir)) {
            require_once __DIR__ . '/cssgenerator.class.php';
            @file_put_contents($cssDir . '/brandkit_login.css', PluginBrandkitslaterCssGenerator::generateLoginCss($fields));
            @file_put_contents($cssDir . '/brandkit_theme.css', PluginBrandkitslaterCssGenerator::generateThemeCss($fields));
        }

        // Grava brandkit_data.js para acesso dinâmico de ícones e logos no frontend
        $jsDir = dirname(__DIR__) . '/js';
        if (is_dir($jsDir)) {
            $jsonData = json_encode([
                'logo_url'            => !empty($fields['logo_url']) ? $fields['logo_url'] : '/plugins/brandkitslater/pics/logo.png?v=' . time(),
                'favicon_url'         => !empty($fields['favicon_url']) ? $fields['favicon_url'] : '/plugins/brandkitslater/pics/favicon.ico?v=' . time(),
                'sidebar_logo_width'  => $fields['sidebar_logo_width'],
                'sidebar_logo_height' => $fields['sidebar_logo_height'],
                'login_logo_width'    => $fields['login_logo_width'],
                'login_logo_height'   => $fields['login_logo_height'],
                'logo_shape'          => $fields['logo_shape'],
                'logo_fit'            => $fields['logo_fit']
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            @file_put_contents($jsDir . '/brandkit_data.js', "window.BRANDKIT_DATA = " . $jsonData . ";\n");
        }

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
