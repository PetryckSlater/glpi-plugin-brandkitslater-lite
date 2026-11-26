<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Compilador e Gerador Dinâmico de CSS v2.2
 * Integração Nativa Profunda com GLPI 10 & GLPI 11 (Tokens --glpi-* e --tblr-*)
 * Suporte Dedicado a Menu Inteiro (Expandido) e Menu Recolhido (70px Rail)
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

class PluginBrandkitslaterCssGenerator {

    /**
     * Converte cor Hex (#003876) para string RGB ("0, 56, 118") compatível com --tblr-primary-rgb
     */
    public static function hexToRgb(string $hex): string {
        $clean = ltrim(trim($hex), '#');
        if (strlen($clean) === 3) {
            $clean = $clean[0] . $clean[0] . $clean[1] . $clean[1] . $clean[2] . $clean[2];
        }
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $clean)) {
            return '0, 56, 118';
        }
        return hexdec(substr($clean, 0, 2)) . ', ' . hexdec(substr($clean, 2, 2)) . ', ' . hexdec(substr($clean, 4, 2));
    }

    /**
     * Gera declarações CSS de formato (shape) para logotipos e ícones
     */
    public static function buildShapeCss(string $shape, string $primary, bool $onDarkSurface = false): string {
        switch ($shape) {
            case 'rounded':
                return "border-radius: 10px !important;";
            case 'circle':
                return "border-radius: 50% !important; aspect-ratio: 1 / 1 !important; object-fit: cover !important;";
            case 'pill':
                return "border-radius: 9999px !important; padding: 3px 8px !important;";
            case 'glass_badge':
                if ($onDarkSurface) {
                    return "border-radius: 12px !important; padding: 6px !important; background: rgba(255, 255, 255, 0.12) !important; border: 1px solid rgba(255, 255, 255, 0.22) !important; backdrop-filter: blur(8px) !important; -webkit-backdrop-filter: blur(8px) !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;";
                }
                return "border-radius: 12px !important; padding: 6px !important; background: rgba(15, 23, 42, 0.06) !important; border: 1px solid rgba(15, 23, 42, 0.12) !important;";
            case 'bordered':
                if ($onDarkSurface) {
                    return "border: 1.5px solid rgba(255, 255, 255, 0.35) !important; padding: 5px !important; background-color: rgba(255, 255, 255, 0.95) !important; border-radius: 12px !important; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18) !important;";
                }
                return "border: 2px solid {$primary} !important; padding: 6px !important; background-color: rgba(255, 255, 255, 0.96) !important; border-radius: 14px !important; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;";
            case 'glow':
                return "filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.55)) !important;";
            default:
                return "";
        }
    }

    /**
     * Gera o CSS dinâmico para a Tela de Login (Glassmorphism 2.0 + 2FA/MFA)
     */
    public static function generateLoginCss(array $c): string {
        $pos = $c['login_position'] ?? 'right';
        $align = 'flex-end';
        $padding = '2.5rem 6vw 2.5rem 2rem';
        $containerMargin = 'margin-left: auto !important; margin-right: 0 !important;';

        if ($pos === 'center') {
            $align = 'center';
            $padding = '2.5rem 2rem';
            $containerMargin = 'margin-left: auto !important; margin-right: auto !important;';
        } elseif ($pos === 'left') {
            $align = 'flex-start';
            $padding = '2.5rem 2rem 2.5rem 6vw';
            $containerMargin = 'margin-left: 0 !important; margin-right: auto !important;';
        }

        $bgUrl = !empty($c['bg_image_url']) ? $c['bg_image_url'] : '/plugins/brandkitslater/pics/login_bg.jpg';
        $overlayOp = max(0, min(95, (int)($c['bg_overlay_opacity'] ?? 75))) / 100;
        $cardWidth = (int)($c['card_width'] ?? 440);
        $cardRadius = (int)($c['card_radius'] ?? 22);
        $cardBlur = (int)($c['card_blur'] ?? 24);
        $cardOp = max(50, min(100, (int)($c['card_opacity'] ?? 94))) / 100;
        $primary = $c['primary_color'] ?? '#1e293b';
        $primaryRgb = self::hexToRgb($primary);
        $primaryHover = $c['primary_hover'] ?? '#334155';
        $accent = $c['accent_color'] ?? '#6366f1';
        $font = $c['font_family'] ?? 'Plus Jakarta Sans';
        $customCss = $c['custom_css'] ?? '';

        $badgeText = isset($c['login_badge_text']) ? trim((string)$c['login_badge_text']) : 'Central de Atendimento & Suporte';
        $escapedBadge = str_replace(["\\", "'", "\n", "\r"], ["\\\\", "\\'", " ", ""], $badgeText);

        // Estilo do Card
        $cardBg = "rgba(255, 255, 255, {$cardOp})";
        $cardBackdrop = "blur({$cardBlur}px) saturate(190%)";
        $cardBorder = "1px solid rgba(255, 255, 255, 0.65)";

        if (($c['card_style'] ?? '') === 'flat') {
            $cardBg = "#ffffff";
            $cardBackdrop = "none";
            $cardBorder = "1px solid #e2e8f0";
        } elseif (($c['card_style'] ?? '') === 'bordered') {
            $cardBg = "#ffffff";
            $cardBackdrop = "none";
            $cardBorder = "2px solid {$primary}";
        }

        $loginLogoW = (int)($c['login_logo_width'] ?? 220);
        $loginLogoH = (int)($c['login_logo_height'] ?? 60);
        $logoShape  = $c['logo_shape'] ?? 'standard';
        $logoFit    = $c['logo_fit'] ?? 'contain';
        $shapeCss   = self::buildShapeCss($logoShape, $primary, false);

        $logoCss = "
.card.card-md > .card-header:first-child,
.card.main-content-card > .card-header:first-child,
.card > .card-header:first-child {
    display: none !important;
}

.brandkit-card-logo {
    display: block !important;
    visibility: visible !important;
    max-width: {$loginLogoW}px !important;
    max-height: {$loginLogoH}px !important;
    width: auto !important;
    height: auto !important;
    margin: 0 auto 1.25rem auto !important;
    object-fit: {$logoFit} !important;
    box-sizing: border-box !important;
    transition: all 0.25s ease !important;
    {$shapeCss}
}
";

        if ($badgeText !== '') {
            $badgeRule = "
.card-body::before,
body.welcome-anonymous .card-body::before,
body.page-anonymous .card-body::before,
body:has(input[name=\"login_name\"]) .card-body::before {
    content: '{$escapedBadge}' !important;
    display: table !important;
    margin: 0 auto 1.25rem auto !important;
    padding: 6px 16px !important;
    border-radius: 10px !important;
    background: #0A1428 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-align: center !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}";
        } else {
            $badgeRule = "
.card-body::before,
body.welcome-anonymous .card-body::before,
body.page-anonymous .card-body::before {
    display: none !important;
    content: none !important;
}";
        }

        return "
/* =============================================================================
   BrandKitSlater v2.2 - Dynamic Login Theme (Glassmorphism 2.0)
   ============================================================================= */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap');

/* 1. FUNDO DA PÁGINA */
body.page-anonymous,
body.welcome-anonymous,
body:has(input[name=\"login_name\"]),
body:has(#login_name) {
    background-color: #0A1428 !important;
    background-image: url('{$bgUrl}') !important;
    background-size: cover !important;
    background-position: left center !important;
    background-repeat: no-repeat !important;
    background-attachment: fixed !important;
    min-height: 100vh !important;
    position: relative !important;
    font-family: '{$font}', -apple-system, BlinkMacSystemFont, sans-serif !important;
    overflow-x: hidden !important;
}

/* 2. VINHETA CINEMATOGRÁFICA DE CONTRASTE */
body.page-anonymous::before,
body.welcome-anonymous::before,
body:has(input[name=\"login_name\"])::before,
body:has(#login_name)::before {
    content: '' !important;
    position: fixed !important;
    top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important;
    background: linear-gradient(
        90deg,
        rgba(10, 20, 40, 0.15) 0%,
        rgba(10, 20, 40, 0.25) 35%,
        rgba(10, 20, 40, 0.65) 65%,
        rgba(10, 20, 40, {$overlayOp}) 100%
    ) !important;
    z-index: 0 !important;
    pointer-events: none !important;
}

/* 3. ALINHAMENTO DO CONTÊINER (GLPI 10 & GLPI 11) */
main#page.page-anonymous,
.page-anonymous,
.welcome-anonymous {
    min-height: 100vh !important;
    position: relative !important;
    z-index: 1 !important;
    background: transparent !important;
    box-sizing: border-box !important;
}

main#page > .flex-fill,
body.welcome-anonymous .flex-fill,
body.page-anonymous .flex-fill,
.page.page-center {
    min-height: 100vh !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: {$align} !important;
    padding: {$padding} !important;
    background: transparent !important;
    box-sizing: border-box !important;
}

/* 4. OCULTA LOGO NATIVO SOLTO FORA DO CARD */
.container-tight > .text-center:first-child,
.container-tight > .text-center:has(.glpi-logo),
.container-tight > .text-center:has([class*=\"glpi-logo\"]),
.container-tight > div:has(.glpi-logo),
.glpi-logo,
span.glpi-logo,
[class*=\"glpi-logo\"],
body:has(input[name=\"login_name\"]) a.navbar-brand:not(:has(.brandkit-card-logo)),
body:has(input[name=\"login_name\"]) .navbar-brand:not(:has(.brandkit-card-logo)),
body:has(input[name=\"login_name\"]) img[src*=\"logo-GLPI\"]:not(.brandkit-card-logo),
body:has(input[name=\"login_name\"]) img[src*=\"logo-glpi\"]:not(.brandkit-card-logo),
body:has(input[name=\"login_name\"]) img[alt=\"GLPI\"]:not(.brandkit-card-logo) {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    width: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    overflow: hidden !important;
}

/* 5. LARGURA E POSICIONAMENTO DO CONTAINER */
.container-tight,
body.welcome-anonymous .container-tight,
body.page-anonymous .container-tight,
.page-anonymous .container {
    max-width: {$cardWidth}px !important;
    width: 100% !important;
    {$containerMargin}
    padding: 0 !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    box-sizing: border-box !important;
}

@keyframes bksCardEntrance {
    0% {
        opacity: 0;
        transform: translateY(18px) scale(0.985);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* 6. CARTÃO DE LOGIN GLASSMORPHISM 2.0 */
.card.card-md,
.card.main-content-card,
body.welcome-anonymous .card,
body.page-anonymous .card,
body:has(input[name=\"login_name\"]) .card {
    background: {$cardBg} !important;
    backdrop-filter: {$cardBackdrop} !important;
    -webkit-backdrop-filter: {$cardBackdrop} !important;
    border: {$cardBorder} !important;
    border-radius: {$cardRadius}px !important;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.45), 0 20px 45px -10px rgba(0, 10, 30, 0.45), 0 40px 80px -20px rgba(0, 0, 0, 0.35) !important;
    overflow: hidden !important;
    position: relative !important;
    width: 100% !important;
    box-sizing: border-box !important;
    animation: bksCardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
}

@media (prefers-reduced-motion: reduce) {
    .card.card-md,
    .card.main-content-card,
    body.welcome-anonymous .card,
    body.page-anonymous .card {
        animation: none !important;
    }
}

/* Faixa Superior de Destaque */
.card.card-md::before,
.card.main-content-card::before,
body.welcome-anonymous .card::before,
body.page-anonymous .card::before,
body:has(input[name=\"login_name\"]) .card::before {
    content: '' !important;
    display: block !important;
    height: 6px !important;
    width: 100% !important;
    background: linear-gradient(90deg, {$primary} 0%, {$accent} 100%) !important;
}

{$logoCss}

/* Corpo do Cartão */
.card-body,
body.welcome-anonymous .card-body,
body.page-anonymous .card-body,
body:has(input[name=\"login_name\"]) .card-body {
    padding: 2.25rem 2.25rem 1.85rem 2.25rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Selo Superior Customizável */
{$badgeRule}

/* 7. EXPANSÃO TOTAL DAS COLUNAS INTERNAS */
.card-body form,
body.page-anonymous .card-body form,
body.welcome-anonymous .card-body form {
    width: 100% !important;
    display: block !important;
}

.card-body form .row,
.card-body .row,
body.page-anonymous .card-body .row,
body.welcome-anonymous .card-body .row {
    display: flex !important;
    flex-direction: column !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

.card-body form .row > [class*=\"col\"],
.card-body .row > [class*=\"col\"],
body.page-anonymous .card-body [class*=\"col-\"],
body.welcome-anonymous .card-body [class*=\"col-\"],
body.page-anonymous .card-body .col,
body.welcome-anonymous .card-body .col {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    padding: 0 !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

/* 8. TÍTULOS E SUBTÍTULOS */
.card-body .card-header,
.card-body [class*=\"col\"] .card-header {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 0 1.25rem 0 !important;
    box-shadow: none !important;
    display: block !important;
    width: 100% !important;
    text-align: center !important;
}

.card-body .card-header h2,
.card-body h2 {
    color: #0A1428 !important;
    font-weight: 800 !important;
    font-size: 1.35rem !important;
    letter-spacing: -0.02em !important;
    text-align: center !important;
    white-space: normal !important;
    word-break: normal !important;
    line-height: 1.35 !important;
    width: 100% !important;
    margin: 0 !important;
    display: block !important;
}

.card-body .rich_text_container {
    margin-bottom: 1.15rem !important;
    width: 100% !important;
    text-align: center !important;
}

.card-body .rich_text_container div[style*=\"top\"] {
    top: 0 !important;
    position: static !important;
}

.card-body .rich_text_container h2,
.card-body .rich_text_container strong,
.card-body .rich_text_container p {
    font-size: 0.88rem !important;
    color: #64748b !important;
    font-weight: 500 !important;
    letter-spacing: normal !important;
    line-height: 1.4 !important;
    margin: 0 !important;
}

/* 9. CAMPOS E RÓTULOS DO FORMULÁRIO */
.card-body .mb-3,
.card-body .mb-4 {
    width: 100% !important;
    margin-bottom: 1rem !important;
}

.card-body .form-label,
.card-body label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    margin-bottom: 0.45rem !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    width: 100% !important;
}

.card-body .form-control,
.card-body input[type=\"text\"],
.card-body input[type=\"password\"],
.card-body select.form-select,
body.page-anonymous .form-control,
body.welcome-anonymous .form-control {
    background-color: #f8fafc !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    padding: 0.8rem 1.1rem !important;
    font-size: 0.95rem !important;
    color: #0f172a !important;
    width: 100% !important;
    min-width: 100% !important;
    box-sizing: border-box !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    margin-bottom: 0.5rem !important;
}

.card-body .form-control:focus,
.card-body input[type=\"text\"]:focus,
.card-body input[type=\"password\"]:focus {
    background-color: #ffffff !important;
    border-color: {$primary} !important;
    box-shadow: 0 0 0 4px rgba({$primaryRgb}, 0.16), 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    outline: none !important;
}

.card-body .form-check,
body.page-anonymous .form-check,
body.welcome-anonymous .form-check {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-top: 0.75rem !important;
    margin-bottom: 0.5rem !important;
    width: 100% !important;
}

.card-body .form-check-input {
    accent-color: {$primary} !important;
}

.card-body .form-check-label {
    font-size: 0.85rem !important;
    color: #475569 !important;
    font-weight: 500 !important;
    cursor: pointer !important;
}

/* 10. BOTÃO DE LOGIN ENTERPRISE */
.card-body .btn-primary,
.card-body button[type=\"submit\"],
.card-body .form-footer button,
.card-body .form-footer .btn,
body.page-anonymous button[type=\"submit\"] {
    background: linear-gradient(135deg, {$primary} 0%, #0A1428 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 0.9rem 1.5rem !important;
    font-weight: 700 !important;
    font-size: 1rem !important;
    letter-spacing: 0.02em !important;
    color: #ffffff !important;
    box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.3), 0 8px 22px rgba({$primaryRgb}, 0.38) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    width: 100% !important;
    min-width: 100% !important;
    display: block !important;
    margin-top: 1rem !important;
    cursor: pointer !important;
    text-align: center !important;
    box-sizing: border-box !important;
}

.card-body .btn-primary:hover,
.card-body button[type=\"submit\"]:hover {
    background: linear-gradient(135deg, {$primaryHover} 0%, #001f3f 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.4), 0 12px 28px rgba({$primaryRgb}, 0.50) !important;
}

.card-body .btn-primary:active,
.card-body button[type=\"submit\"]:active {
    transform: scale(0.98) !important;
}

/* 11. LINKS E ESQUECEU A SENHA */
.card-body .forgot_password,
.card-body a {
    color: {$primary} !important;
    font-weight: 600 !important;
    font-size: 0.85rem !important;
    text-decoration: none !important;
    transition: color 0.15s ease !important;
}

.card-body a:hover {
    color: {$accent} !important;
    text-decoration: underline !important;
}

/* 12. BOTÃO DE AUTO-CADASTRO */
.brandkit-login-register-wrapper {
    width: 100% !important;
    box-sizing: border-box !important;
    margin-top: 1.25rem !important;
}

.brandkit-register-btn {
    width: 100% !important;
    box-sizing: border-box !important;
    display: inline-flex !important;
}

/* 13. RODAPÉ DE COPYRIGHT */
.container-tight > .text-muted,
.page-anonymous .text-muted.mt-3 {
    background: rgba(10, 20, 40, 0.65) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    padding: 0.65rem 1.25rem !important;
    border-radius: 12px !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25) !important;
    display: block !important;
    text-align: center !important;
    margin-top: 1.25rem !important;
}

.container-tight > .text-muted a,
.page-anonymous .text-muted a {
    color: rgba(255, 255, 255, 0.75) !important;
    font-size: 0.8rem !important;
    font-weight: 500 !important;
    text-decoration: none !important;
}

/* 14. RESPONSIVIDADE */
@media (max-width: 991px) {
    body.page-anonymous::before,
    body.welcome-anonymous::before,
    body:has(input[name=\"login_name\"])::before {
        background: linear-gradient(
            180deg,
            rgba(10, 20, 40, 0.30) 0%,
            rgba(10, 20, 40, 0.85) 100%
        ) !important;
    }

    main#page > .flex-fill,
    body.welcome-anonymous .flex-fill,
    body.page-anonymous .flex-fill,
    .page.page-center {
        align-items: center !important;
        justify-content: center !important;
        padding: 1.5rem !important;
    }

    .container-tight,
    body.welcome-anonymous .container-tight,
    body.page-anonymous .container-tight {
        margin: 0 auto !important;
        max-width: 100% !important;
    }
}

/* 15. SUPORTE TOTAL A AUTENTICAÇÃO DE DOIS FATORES (2FA / TOTP / MFA) */
.card:has([data-code-type]) > .card-header:first-child,
.card:has(input[name=\"totp_code[]\"]) > .card-header:first-child,
.card:has([action*=\"MFA\"]) > .card-header:first-child,
.card:has(#backup_codes) > .card-header:first-child,
body:has([data-code-type]) .card-header,
body:has(input[name=\"totp_code[]\"]) .card-header,
body:has([action*=\"MFA\"]) .card-header,
body:has(#backup_codes) .card-header {
    display: block !important;
    background: transparent !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    padding: 1.25rem 2rem 0.75rem 2rem !important;
    text-align: center !important;
}

.card:has([data-code-type]) .card-title,
.card:has(input[name=\"totp_code[]\"]) .card-title,
.card:has([action*=\"MFA\"]) .card-title,
.card:has(#backup_codes) .card-title,
body:has([data-code-type]) .card-title,
body:has(input[name=\"totp_code[]\"]) .card-title,
body:has([action*=\"MFA\"]) .card-title,
body:has(#backup_codes) .card-title {
    display: block !important;
    color: #0A1428 !important;
    font-size: 1.15rem !important;
    font-weight: 700 !important;
    letter-spacing: -0.01em !important;
    text-align: center !important;
    margin: 0 !important;
    line-height: 1.4 !important;
}

form[data-code-type],
form[data-code-type].m-md-n4,
form:has(input[name=\"totp_code[]\"]),
form:has(input[name=\"totp_code[]\"]).m-md-n4 {
    margin: 0 !important;
    width: 100% !important;
}

[data-code-type=\"code\"] .d-flex.justify-content-center,
form:has(input[name=\"totp_code[]\"]) .d-flex.justify-content-center,
.card-body .d-flex[dir=\"ltr\"] {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 1.5rem 0 !important;
    width: 100% !important;
    flex-wrap: nowrap !important;
}

input[name=\"totp_code[]\"],
.card-body input[name=\"totp_code[]\"],
body.welcome-anonymous input[name=\"totp_code[]\"],
body.page-anonymous input[name=\"totp_code[]\"],
.card-body .form-control[name=\"totp_code[]\"] {
    width: 2.85rem !important;
    min-width: 2.85rem !important;
    max-width: 2.85rem !important;
    height: 3.5rem !important;
    font-size: 1.6rem !important;
    font-weight: 800 !important;
    text-align: center !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-block !important;
    border-radius: 12px !important;
    border: 2px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    color: #0A1428 !important;
    box-sizing: border-box !important;
    transition: all 0.2s ease !important;
    flex: 0 0 2.85rem !important;
}

input[name=\"totp_code[]\"]:focus,
.card-body input[name=\"totp_code[]\"]:focus {
    border-color: {$primary} !important;
    box-shadow: 0 0 0 4px rgba({$primaryRgb}, 0.18) !important;
    background-color: #ffffff !important;
    outline: none !important;
}

input[name=\"backup_code\"],
.card-body input[name=\"backup_code\"],
.card-body .form-control[name=\"backup_code\"] {
    width: 100% !important;
    min-width: 100% !important;
    margin: 1.25rem 0 !important;
    text-align: center !important;
    font-family: 'Courier New', Courier, monospace !important;
    font-size: 1.2rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.15em !important;
    border-radius: 12px !important;
    padding: 0.85rem 1rem !important;
}

form[data-code-type] .border-top,
form:has(input[name=\"totp_code[]\"]) .border-top {
    border-top: 1px solid #e2e8f0 !important;
    margin-top: 1.75rem !important;
    padding-top: 1.25rem !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 12px !important;
}

form[data-code-type] .border-top > .ms-auto,
form:has(input[name=\"totp_code[]\"]) .border-top > .ms-auto {
    margin: 0 !important;
    width: 100% !important;
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 0 !important;
}

.js-tfa-switch-code-type,
button.js-tfa-switch-code-type,
.card-body .btn-link.js-tfa-switch-code-type,
form[data-code-type] .btn-link {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: {$primary} !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0 !important;
    width: auto !important;
    min-width: auto !important;
    display: inline-block !important;
    margin: 0 !important;
    cursor: pointer !important;
    text-align: left !important;
    text-decoration: none !important;
}

.js-tfa-switch-code-type:hover,
button.js-tfa-switch-code-type:hover {
    color: {$accent} !important;
    text-decoration: underline !important;
    transform: none !important;
    box-shadow: none !important;
}

form[data-code-type] button[name=\"totp_cancel\"],
form:has(input[name=\"totp_code[]\"]) button[name=\"totp_cancel\"],
.card-body button[name=\"totp_cancel\"] {
    background: #f1f5f9 !important;
    border: 1px solid #cbd5e1 !important;
    color: #475569 !important;
    font-weight: 600 !important;
    border-radius: 10px !important;
    padding: 0.65rem 1.25rem !important;
    font-size: 0.92rem !important;
    width: auto !important;
    min-width: 100px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-top: 0 !important;
    box-shadow: none !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}

form[data-code-type] button[name=\"continue\"],
form:has(input[name=\"totp_code[]\"]) button[name=\"continue\"],
.card-body button[name=\"continue\"] {
    background: linear-gradient(135deg, {$primary} 0%, #0A1428 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    border-radius: 10px !important;
    padding: 0.65rem 1.4rem !important;
    font-size: 0.92rem !important;
    width: auto !important;
    min-width: 120px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-top: 0 !important;
    box-shadow: 0 4px 14px rgba({$primaryRgb}, 0.3) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}

body:has([action*=\"MFA\"]) .container-tight,
body:has([action*=\"2fa\"]) .container-tight,
body:has(#backup_codes) .container-tight {
    max-width: 540px !important;
}

body:has([action*=\"MFA\"]) img,
body:has([action*=\"2fa\"]) img {
    display: block !important;
    margin: 1.25rem auto !important;
    border-radius: 14px !important;
    border: 4px solid #ffffff !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

body:has([data-code-type]) .brandkit-login-register-wrapper,
body:has(input[name=\"totp_code[]\"]) .brandkit-login-register-wrapper,
body:has([action*=\"MFA\"]) .brandkit-login-register-wrapper,
body:has(#backup_codes) .brandkit-login-register-wrapper,
body:has([data-code-type]) .card-body::before,
body:has(input[name=\"totp_code[]\"]) .card-body::before,
body:has([action*=\"MFA\"]) .card-body::before,
body:has(#backup_codes) .card-body::before {
    display: none !important;
}

{$customCss}
";
    }

    /**
     * Gera o CSS dinâmico para o Painel Interno do GLPI 10 & GLPI 11
     * Camada 1: Logotipos do Menu Inteiro (Expandido) & Ícone do Menu Recolhido (70px)
     * Camada 2: Tokens Nativos GLPI (--glpi-* / --tblr-*), Sidebar, Topbar, Cards e A11y
     */
    public static function generateThemeCss(array $c): string {
        $enableLogos        = !isset($c['enable_custom_logos']) || !empty($c['enable_custom_logos']);
        $enableInternal     = !empty($c['enable_internal_theme']);
        $primary            = $c['primary_color'] ?? '#1e293b';
        $primaryRgb         = self::hexToRgb($primary);
        $primaryHover       = $c['primary_hover'] ?? '#334155';
        $accent             = $c['accent_color'] ?? '#6366f1';
        $customCss          = $c['custom_css'] ?? '';

        $mainLogo           = !empty($c['logo_url']) ? $c['logo_url'] : '/plugins/brandkitslater/pics/logo.png';
        $expandedLogo       = !empty(trim($c['sidebar_logo_url'] ?? '')) ? trim($c['sidebar_logo_url']) : $mainLogo;
        $collapsedLogo      = !empty(trim($c['collapsed_logo_url'] ?? '')) ? trim($c['collapsed_logo_url']) : '/plugins/brandkitslater/pics/logo_collapsed.svg';

        $sidebarLogoW       = max(50, min(260, (int)($c['sidebar_logo_width'] ?? 145)));
        $sidebarLogoH       = max(20, min(90, (int)($c['sidebar_logo_height'] ?? 40)));
        $sidebarLogoShape   = $c['sidebar_logo_shape'] ?? ($c['logo_shape'] ?? 'standard');
        $sidebarLogoBgPill  = !empty($c['sidebar_logo_bg_pill']);

        $collapsedSize      = max(22, min(56, (int)($c['collapsed_logo_size'] ?? 38)));
        $collapsedShape     = $c['collapsed_logo_shape'] ?? 'rounded';
        $logoFit            = $c['logo_fit'] ?? 'contain';

        $expandedShapeCss   = self::buildShapeCss($sidebarLogoShape, $primary, true);
        $collapsedShapeCss  = self::buildShapeCss($collapsedShape, $primary, true);

        $pillCss = $sidebarLogoBgPill
            ? "background: rgba(255, 255, 255, 0.94) !important; padding: 5px 10px !important; border-radius: 10px !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.16) !important;"
            : "";

        $cssOutput = "/* BrandKitSlater v2.2 - Native GLPI Integration Layer */\n";

        // =========================================================================
        // CAMADA 1: MOTOR DUAL DE LOGOTIPO (MENU INTEIRO vs. MENU RECOLHIDO 70PX)
        // Funciona tanto no Modo Híbrido quanto no Modo Tema Completo (Zero-FOUC)
        // =========================================================================
        if ($enableLogos) {
            $cssOutput .= "
:root {
    --glpi-logo: url('{$expandedLogo}') !important;
    --glpi-logo-reduced: url('{$collapsedLogo}') !important;
}

/* Container Size-Aware no cabeçalho da barra lateral (Modern Web Guidance: container-type) */
aside.navbar-vertical .navbar-brand,
.navbar-vertical .navbar-brand {
    container-type: inline-size;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0.45rem 0.5rem !important;
    min-height: 52px !important;
    text-decoration: none !important;
    transition: padding 0.2s ease !important;
}

header.topbar .navbar-brand {
    display: inline-flex !important;
    align-items: center !important;
}

/* Fallback CSS instantâneo antes do JS hidratar as tags <img> */
.page .navbar-brand:not(:has(.brandkit-sidebar-logo)) .glpi-logo {
    background-image: url('{$expandedLogo}') !important;
    background-size: {$logoFit} !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    width: {$sidebarLogoW}px !important;
    height: {$sidebarLogoH}px !important;
}

body.navbar-collapsed .page .navbar-brand:not(:has(.brandkit-sidebar-logo)) .glpi-logo {
    background-image: url('{$collapsedLogo}') !important;
    background-size: {$logoFit} !important;
    background-position: center center !important;
    width: {$collapsedSize}px !important;
    height: {$collapsedSize}px !important;
    margin: 0 auto !important;
}

/* Assim que o JS injeta os elementos dedicados, oculta o span legado sem FOUC */
.navbar-brand:has(.brandkit-sidebar-logo) .glpi-logo {
    display: none !important;
}

/* Logo do Menu Inteiro (Expandido / Horizontal) */
.brandkit-logo-expanded {
    display: block !important;
    max-width: {$sidebarLogoW}px !important;
    max-height: {$sidebarLogoH}px !important;
    width: auto !important;
    height: auto !important;
    object-fit: {$logoFit} !important;
    margin: 0 auto !important;
    transition: opacity 0.2s ease, transform 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    {$expandedShapeCss}
    {$pillCss}
}

/* Ícone do Menu Recolhido (Oculto quando o menu está aberto) */
.brandkit-logo-collapsed {
    display: none !important;
    width: {$collapsedSize}px !important;
    height: {$collapsedSize}px !important;
    max-width: {$collapsedSize}px !important;
    max-height: {$collapsedSize}px !important;
    object-fit: {$logoFit} !important;
    margin: 0 auto !important;
    flex-shrink: 0 !important;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease !important;
    {$collapsedShapeCss}
}

.brandkit-logo-collapsed:hover {
    transform: scale(1.06) !important;
}

/* Troca automática quando o menu lateral do GLPI está recolhido (70px) */
body.navbar-collapsed .navbar-vertical .navbar-brand,
body.menu-collapsed .navbar-vertical .navbar-brand {
    padding: 0.45rem 0 !important;
    margin-inline: 0 !important;
    width: 100% !important;
    justify-content: center !important;
}

body.navbar-collapsed .navbar-vertical .brandkit-logo-expanded,
body.menu-collapsed .navbar-vertical .brandkit-logo-expanded {
    display: none !important;
}

body.navbar-collapsed .navbar-vertical .brandkit-logo-collapsed,
body.menu-collapsed .navbar-vertical .brandkit-logo-collapsed {
    display: block !important;
}

/* Container Query de segurança caso a largura útil do navbar-brand fique <= 90px */
@supports (container-type: inline-size) {
    @container (max-width: 90px) {
        .brandkit-logo-expanded {
            display: none !important;
        }
        .brandkit-logo-collapsed {
            display: block !important;
        }
    }
}
";
        }

        // Se o Tema Interno de Cores estiver desabilitado (Modo Híbrido), encerra aqui mantendo o visual nativo do GLPI!
        if (!$enableInternal) {
            return $cssOutput . "\n/* BrandKitSlater: Modo Híbrido ativo (cores nativas do GLPI preservadas). */\n" . $customCss;
        }

        // =========================================================================
        // CAMADA 2: TEMA INTERNO COMPLETO E PROFUNDO PARA GLPI 10 & GLPI 11
        // =========================================================================
        $navbarBg           = $c['navbar_bg'] ?? '#0A1428';
        $navbarTextColor    = $c['navbar_text_color'] ?? '#ffffff';
        $navbarBorderColor  = !empty($c['navbar_border_color']) ? $c['navbar_border_color'] : $accent;
        $sidebarBg          = $c['sidebar_bg'] ?? '#0A1428';
        $sidebarTextColor   = $c['sidebar_text_color'] ?? '#cbd5e1';
        $sidebarActiveColor = $c['sidebar_active_color'] ?? '#38bdf8';
        $sidebarActiveBg    = $c['sidebar_active_bg'] ?? 'rgba(255, 255, 255, 0.08)';
        $sidebarWidth       = max(200, min(300, (int)($c['sidebar_width'] ?? 240)));
        $sidebarItemStyle   = $c['sidebar_item_style'] ?? 'pill';
        $bodyBg             = $c['body_bg'] ?? '#f8fafc';
        $cardBg             = $c['card_bg'] ?? '#ffffff';
        $uiRadius           = max(0, min(20, (int)($c['ui_border_radius'] ?? 10)));
        $internalCardStyle  = $c['internal_card_style'] ?? 'elevated';
        $styleBrowserUi     = !isset($c['style_scrollbars_selection']) || !empty($c['style_scrollbars_selection']);
        $font               = $c['font_family'] ?? 'Plus Jakarta Sans';

        // Estilo dos itens do menu quando expandido (sem afetar a área de hover do menu recolhido!)
        if ($sidebarItemStyle === 'native') {
            $navItemExpandedCss = "
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link {
    border-radius: 0 !important;
    margin: 1px 0 !important;
    border-left: 3px solid transparent !important;
}
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link.active,
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item.active > .nav-link {
    border-left: 3px solid {$sidebarActiveColor} !important;
}";
        } elseif ($sidebarItemStyle === 'glass') {
            $navItemExpandedCss = "
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link {
    border-radius: {$uiRadius}px !important;
    margin: 3px 10px !important;
    padding: 0.5rem 0.9rem !important;
    border: 1px solid transparent !important;
}
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link.active,
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item.active > .nav-link {
    border: 1px solid rgba(255, 255, 255, 0.16) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}";
        } else {
            // 'pill' (Padrão Moderno SaaS)
            $navItemExpandedCss = "
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link {
    border-radius: {$uiRadius}px !important;
    margin: 2px 10px !important;
    padding: 0.48rem 0.95rem !important;
    border-left: 3px solid transparent !important;
}
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item > .nav-link.active,
body:not(.navbar-collapsed) .navbar-vertical #navbar-menu .nav-item.active > .nav-link {
    border-left: 3px solid {$sidebarActiveColor} !important;
}";
        }

        // Estilo dos Cards Internos do GLPI
        if ($internalCardStyle === 'native') {
            $internalCardsCss = "
.page-body .card:not(.brandkit-card) {
    background-color: {$cardBg};
    border-radius: {$uiRadius}px;
    border: 1px solid rgba(148, 163, 184, 0.25);
}";
        } elseif ($internalCardStyle === 'accent_top') {
            $internalCardsCss = "
.page-body .card:not(.brandkit-card) {
    background-color: {$cardBg};
    border-radius: {$uiRadius}px;
    border: 1px solid rgba(148, 163, 184, 0.22);
    box-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.06);
}
.page-body > main > .card:not(.brandkit-card),
.page-body .tab-content > .tab-pane > .card:not(.brandkit-card) {
    border-top: 3px solid {$primary};
}";
        } else {
            // 'elevated'
            $internalCardsCss = "
.page-body .card:not(.brandkit-card) {
    background-color: {$cardBg};
    border-radius: {$uiRadius}px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.06), 0 1px 3px rgba(15, 23, 42, 0.03);
}";
        }

        $browserUiCss = "";
        if ($styleBrowserUi) {
            $browserUiCss = "
/* Browser-Generated UI Theming (Modern Web Guidance: browser-ui-theming) */
::selection {
    background-color: rgba({$primaryRgb}, 0.22);
    color: inherit;
}

input[type=\"checkbox\"]:not(.form-check-input),
input[type=\"radio\"]:not(.form-check-input),
progress {
    accent-color: {$primary};
}

:root {
    scrollbar-color: rgba(100, 116, 139, 0.55) transparent;
}
";
        }

        $cssOutput .= "
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap');

:root {
    --bks-primary: {$primary};
    --bks-primary-rgb: {$primaryRgb};
    --bks-primary-hover: {$primaryHover};
    --bks-accent: {$accent};
    --bks-navbar-bg: {$navbarBg};
    --bks-navbar-text: {$navbarTextColor};
    --bks-navbar-border: {$navbarBorderColor};
    --bks-sidebar-bg: {$sidebarBg};
    --bks-sidebar-text: {$sidebarTextColor};
    --bks-sidebar-active: {$sidebarActiveColor};
    --bks-sidebar-active-bg: {$sidebarActiveBg};
    --bks-body-bg: {$bodyBg};
    --bks-card-bg: {$cardBg};
    --bks-radius: {$uiRadius}px;

    /* Variáveis Nativas Oficiais do GLPI 11 & Tabler */
    --glpi-mainmenu-bg: {$sidebarBg} !important;
    --glpi-mainmenu-fg: {$sidebarTextColor} !important;
    --glpi-mainmenu-fg-muted: color-mix(in srgb, {$sidebarTextColor}, transparent 28%) !important;
    --glpi-mainmenu-active-bg: {$sidebarActiveBg} !important;
    --glpi-tabs-active-border-color: {$primary} !important;
    --tblr-primary: {$primary} !important;
    --tblr-primary-rgb: {$primaryRgb} !important;
    --tblr-link-color: {$primary} !important;
    --tblr-link-hover-color: {$primaryHover} !important;
    --tblr-border-radius: {$uiRadius}px !important;
    --tblr-font-sans-serif: '{$font}', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

body, .page {
    font-family: var(--tblr-font-sans-serif) !important;
    background-color: {$bodyBg} !important;
}

{$browserUiCss}

/* 1. BARRA SUPERIOR (HEADER NAVBAR & TOPBAR) */
header.navbar,
header.topbar,
#header {
    background: {$navbarBg} !important;
    border-bottom: 3px solid {$navbarBorderColor} !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.10) !important;
}

header.navbar .nav-link,
header.navbar .navbar-brand,
header.navbar .nav-link-title,
header.navbar .nav-link-icon,
header.navbar .breadcrumb-item,
header.navbar .breadcrumb-item a,
header.navbar .breadcrumb-item.active,
header.navbar .ti,
header.navbar .fas,
header.navbar .far {
    color: {$navbarTextColor} !important;
}

header.navbar .breadcrumb-item + .breadcrumb-item::before {
    color: color-mix(in srgb, {$navbarTextColor}, transparent 45%) !important;
}

/* Campo de busca global na barra superior */
header.navbar .input-icon .form-control,
header.navbar input[name=\"globalsearch\"] {
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: {$navbarTextColor} !important;
    border-radius: {$uiRadius}px !important;
}

header.navbar .input-icon .form-control::placeholder,
header.navbar input[name=\"globalsearch\"]::placeholder {
    color: color-mix(in srgb, {$navbarTextColor}, transparent 35%) !important;
}

header.navbar .input-icon .form-control:focus {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: {$sidebarActiveColor} !important;
    box-shadow: 0 0 0 3px rgba({$primaryRgb}, 0.25) !important;
}

/* 2. BARRA LATERAL (SIDEBAR VERTICAL EXPANDIDA & RECOLHIDA 70PX) */
aside.sidebar,
.navbar-vertical,
aside.navbar-vertical {
    background: {$sidebarBg} !important;
    border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
}

@media (min-width: 992px) {
    body.vertical-layout:not(.navbar-collapsed) aside.sidebar {
        width: {$sidebarWidth}px !important;
    }

    body.vertical-layout:not(.navbar-collapsed) aside.sidebar ~ .page-wrapper,
    body.vertical-layout:not(.navbar-collapsed) aside.sidebar ~ header.navbar {
        margin-inline-start: {$sidebarWidth}px !important;
    }

    body.vertical-layout.navbar-collapsed aside.sidebar {
        width: 70px !important;
    }

    body.vertical-layout.navbar-collapsed aside.sidebar ~ .page-wrapper,
    body.vertical-layout.navbar-collapsed aside.sidebar ~ header.navbar {
        margin-inline-start: 70px !important;
    }
}

.navbar-vertical #navbar-menu .nav-item > .nav-link {
    color: {$sidebarTextColor} !important;
    transition: background-color 0.18s ease, color 0.18s ease, border-color 0.18s ease !important;
}

.navbar-vertical #navbar-menu .nav-item > .nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.09) !important;
}

.navbar-vertical #navbar-menu .nav-item > .nav-link.active,
.navbar-vertical #navbar-menu .nav-item.active > .nav-link,
.navbar-vertical #navbar-menu .nav-item > .nav-link.show {
    color: {$sidebarActiveColor} !important;
    background: {$sidebarActiveBg} !important;
    font-weight: 700 !important;
}

{$navItemExpandedCss}

/* Botões utilitários da Sidebar: 'Encontrar um menu' (.trigger-fuzzy) e 'Recolher menu' (.reduce-menu) */
.navbar-vertical .trigger-fuzzy,
.navbar-vertical .reduce-menu {
    color: {$sidebarTextColor} !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}

.navbar-vertical .trigger-fuzzy:hover,
.navbar-vertical .reduce-menu:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.1) !important;
}

/* Submenus internos abertos na sidebar expandida */
body:not(.navbar-collapsed) .sidebar #navbar-menu .nav-item .nav-link + .dropdown-menu {
    background-color: rgba(0, 0, 0, 0.18) !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0.25rem 0 !important;
}

body:not(.navbar-collapsed) .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item {
    color: {$sidebarTextColor} !important;
    font-size: 0.85rem !important;
    padding: 0.42rem 1rem 0.42rem 2.25rem !important;
    border-radius: 0 !important;
}

body:not(.navbar-collapsed) .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item:hover,
body:not(.navbar-collapsed) .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item.active {
    color: {$sidebarActiveColor} !important;
    background: rgba(255, 255, 255, 0.07) !important;
}

/* Flyout flutuante quando a Sidebar está RECOLHIDA (70px) - sem gap de hover! */
body.navbar-collapsed .sidebar #navbar-menu .nav-item > .nav-link {
    margin: 0 !important;
    border-radius: 0 !important;
    justify-content: center !important;
    padding: 0.65rem 0 !important;
}

body.navbar-collapsed .sidebar #navbar-menu .nav-item > .nav-link i,
body.navbar-collapsed .sidebar #navbar-menu .nav-item > .nav-link .nav-link-icon {
    margin: 0 !important;
    font-size: 1.15rem !important;
}

body.navbar-collapsed .sidebar #navbar-menu .nav-item .nav-link + .dropdown-menu {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 0 {$uiRadius}px {$uiRadius}px 0 !important;
    box-shadow: 0 16px 32px -6px rgba(15, 23, 42, 0.22) !important;
    padding: 0.5rem !important;
    min-width: 210px !important;
}

body.navbar-collapsed .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-header {
    color: {$primary} !important;
    font-weight: 800 !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    border-bottom: 1px solid #f1f5f9 !important;
    margin-bottom: 0.25rem !important;
}

body.navbar-collapsed .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item {
    color: #1e293b !important;
    border-radius: 6px !important;
    padding: 0.45rem 0.75rem !important;
    font-weight: 500 !important;
}

body.navbar-collapsed .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item:hover,
body.navbar-collapsed .sidebar #navbar-menu .nav-item .dropdown-menu .dropdown-item.active {
    color: {$primary} !important;
    background-color: rgba({$primaryRgb}, 0.08) !important;
}

/* 3. MENUS SUSPENSOS DA NAVBAR SUPERIOR */
header.navbar .dropdown-menu {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 12px 28px -5px rgba(15, 23, 42, 0.16) !important;
    border-radius: {$uiRadius}px !important;
    padding: 0.45rem !important;
}

header.navbar .dropdown-menu .dropdown-item {
    color: #1e293b !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
    border-radius: 6px !important;
    padding: 0.48rem 0.85rem !important;
}

header.navbar .dropdown-menu .dropdown-item:hover {
    color: {$primary} !important;
    background-color: #f1f5f9 !important;
}

/* 4. SUPERFÍCIES, CARTÕES E BOTÕES DO GLPI */
{$internalCardsCss}

.btn-primary,
button.btn-primary,
a.btn-primary,
input[type=\"submit\"].btn-primary {
    --tblr-btn-color: #ffffff !important;
    --tblr-btn-hover-color: #ffffff !important;
    --tblr-btn-active-color: #ffffff !important;
    background-color: {$primary} !important;
    border-color: {$primary} !important;
    color: #ffffff !important;
    border-radius: {$uiRadius}px !important;
    font-weight: 600 !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active {
    background-color: {$primaryHover} !important;
    border-color: {$primaryHover} !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
}

.btn-outline-primary {
    color: {$primary} !important;
    border-color: {$primary} !important;
    border-radius: {$uiRadius}px !important;
}

.btn-outline-primary:hover {
    background-color: {$primary} !important;
    border-color: {$primary} !important;
    color: #ffffff !important;
}

.form-check-input:checked {
    background-color: {$primary} !important;
    border-color: {$primary} !important;
}

.nav-tabs .nav-link.active {
    border-bottom-color: {$primary} !important;
    color: {$primary} !important;
    font-weight: 700 !important;
}

{$customCss}
";

        return $cssOutput;
    }
}
