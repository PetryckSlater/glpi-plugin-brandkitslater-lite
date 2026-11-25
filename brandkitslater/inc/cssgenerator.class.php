<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Compilador e Gerador Dinâmico de CSS
 * -----------------------------------------------------------------------------
 */

defined('GLPI_ROOT') || die("Sorry. You can't access this file directly");

class PluginBrandkitslaterCssGenerator {

    /**
     * Gera o CSS dinâmico para a Tela de Login
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
        $primary = $c['primary_color'] ?? '#003876';
        $primaryHover = $c['primary_hover'] ?? '#004ba0';
        $accent = $c['accent_color'] ?? '#C0122D';
        $font = $c['font_family'] ?? 'Plus Jakarta Sans';
        $logoUrl = trim($c['logo_url'] ?? '');
        $customCss = $c['custom_css'] ?? '';

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

        // Renderização de Logo Institucional Personalizado (se configurado)
        $loginLogoW = (int)($c['login_logo_width'] ?? 200);
        $loginLogoH = (int)($c['login_logo_height'] ?? 55);
        $logoShape = $c['logo_shape'] ?? 'standard';
        $logoFit = $c['logo_fit'] ?? 'contain';

        $shapeCss = "";
        if ($logoShape === 'rounded') {
            $shapeCss = "border-radius: 12px !important;";
        } elseif ($logoShape === 'circle') {
            $shapeCss = "border-radius: 50% !important; aspect-ratio: 1/1 !important;";
        } elseif ($logoShape === 'pill') {
            $shapeCss = "border-radius: 9999px !important;";
        } elseif ($logoShape === 'bordered') {
            $shapeCss = "border: 2px solid {$primary} !important; padding: 6px !important; background-color: rgba(255, 255, 255, 0.95) !important; border-radius: 14px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;";
        } elseif ($logoShape === 'glow') {
            $shapeCss = "filter: drop-shadow(0 0 10px rgba(0, 149, 183, 0.55)) !important;";
        }

        // Garante que o header vazio nativo do GLPI permaneça oculto e estiliza a imagem do logo
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

        return "
/* =============================================================================
   BrandKitSlater - Dynamic Login Theme (Community Edition)
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

/* 3. ALINHAMENTO DO CONTÊINER E SUPORTE A GLPI 10 E GLPI 11 */
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

/* 4. ELIMINA DEFINITIVAMENTE O LOGO SOLTO DO GLPI NO CÉU */
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

/* 5. LARGURA E POSICIONAMENTO DO CONTAINER-TIGHT */
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
        transform: translateY(20px) scale(0.985);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* 6. CARTÃO DE LOGIN GLASSMORPHISM 2.0 PREMIUM */
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

/* Faixa Superior Accent */
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

/* Logo customizado no topo do card */
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

/* Selo Superior de Atendimento */
.card-body::before,
body.welcome-anonymous .card-body::before,
body.page-anonymous .card-body::before,
body:has(input[name=\"login_name\"]) .card-body::before {
    content: 'Central de Atendimento & Suporte' !important;
    display: table !important;
    margin: 0 auto 1.25rem auto !important;
    padding: 6px 16px !important;
    border-radius: 10px !important;
    background: {$primary} !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.04em !important;
    text-align: center !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

/* 7. EXPANSÃO TOTAL DAS COLUNAS INTERNAS (FIM DO FORMULÁRIO ESPREMIDO) */
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

/* 8. TÍTULO E SUBTÍTULOS ELEGANTES (SEM QUEBRAS VERTICAIS!) */
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

/* Mensagem de Boas-Vindas do GLPI (Subtítulo elegante) */
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

/* 9. CAMPOS DE ENTRADA (INPUTS MODERNOS FULL-WIDTH) */
.card-body .form-label,
body.page-anonymous .form-label,
body.welcome-anonymous .form-label {
    color: #334155 !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    margin-bottom: 0.4rem !important;
    display: block !important;
    text-align: left !important;
    width: 100% !important;
}

.card-body .form-control,
.card-body input[type=\"text\"],
.card-body input[type=\"password\"],
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
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    margin-bottom: 0.5rem !important;
}

.card-body .form-control:focus,
.card-body input[type=\"text\"]:focus,
.card-body input[type=\"password\"]:focus {
    background-color: #ffffff !important;
    border-color: {$primary} !important;
    box-shadow: 0 0 0 4px rgba(0, 56, 118, 0.16), 0 1px 2px rgba(0,0,0,0.05) !important;
    outline: none !important;
}

/* Checkbox Lembrar de Mim */
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

.card-body .form-check-label {
    font-size: 0.85rem !important;
    color: #475569 !important;
    font-weight: 500 !important;
    cursor: pointer !important;
}

/* 10. BOTÃO DE LOGIN ENTERPRISE (LARGURA TOTAL 100%) */
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
    box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.3), 0 8px 22px rgba(0, 56, 118, 0.38) !important;
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
    box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.4), 0 12px 28px rgba(0, 56, 118, 0.50) !important;
}

.card-body .btn-primary:active,
.card-body button[type=\"submit\"]:active {
    transform: scale(0.98) !important;
}

/* 11. LINKS DE ESQUECEU A SENHA */
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
    box-shadow: 0 0 0 4px rgba(0, 56, 118, 0.18) !important;
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

form[data-code-type] button[name=\"totp_cancel\"]:hover,
form:has(input[name=\"totp_code[]\"]) button[name=\"totp_cancel\"]:hover,
.card-body button[name=\"totp_cancel\"]:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
    transform: translateY(-1px) !important;
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
    box-shadow: 0 4px 14px rgba(0, 56, 118, 0.3) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}

form[data-code-type] button[name=\"continue\"]:hover,
form:has(input[name=\"totp_code[]\"]) button[name=\"continue\"]:hover,
.card-body button[name=\"continue\"]:hover {
    background: linear-gradient(135deg, {$primaryHover} 0%, #001f3f 100%) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(0, 56, 118, 0.45) !important;
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

body:has([action*=\"MFA\"]) .btn-group,
body:has([action*=\"2fa\"]) .btn-group {
    display: flex !important;
    width: 100% !important;
    margin: 0.75rem 0 !important;
}

body:has([action*=\"MFA\"]) .btn-group .btn-icon,
body:has([action*=\"2fa\"]) .btn-group .btn-icon {
    width: auto !important;
    min-width: auto !important;
    padding: 0.6rem 1rem !important;
    background: {$primary} !important;
    color: #ffffff !important;
    border-radius: 0 10px 10px 0 !important;
    margin-top: 0 !important;
}

#backup_codes {
    width: 100% !important;
}

#backup_codes table {
    margin: 1rem auto !important;
    width: 100% !important;
    max-width: 320px !important;
}

#backup_codes td.backup-code {
    font-family: monospace !important;
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.1em !important;
    padding: 0.5rem 1rem !important;
}

body:has(#backup_codes) form.d-flex,
form.d-flex.flex-row-reverse {
    display: flex !important;
    flex-direction: row-reverse !important;
    width: 100% !important;
    margin-top: 1.5rem !important;
}

body:has(#backup_codes) form.d-flex button[type=\"submit\"],
form.d-flex.flex-row-reverse button[type=\"submit\"] {
    width: auto !important;
    min-width: 140px !important;
    margin-top: 0 !important;
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
     * Gera o CSS dinâmico para o Painel Interno (Navbar, Cores, Fontes, Sidebar e Logos)
     */
    public static function generateThemeCss(array $c): string {
        // Se o tema interno não estiver explicitamente habilitado, não injeta regras que alterem a navbar ou layout original
        if (empty($c['enable_internal_theme'])) {
            return "/* BrandKitSlater: Tema interno desabilitado. O GLPI está utilizando 100% o seu layout nativo (Light Mode). */\n" . (!empty($c['custom_css']) ? $c['custom_css'] : '');
        }

        $primary            = $c['primary_color'] ?? '#003876';
        $primaryHover       = $c['primary_hover'] ?? '#004ba0';
        $accent             = $c['accent_color'] ?? '#C0122D';
        $navbarBg           = $c['navbar_bg'] ?? '#0A1428';
        $navbarTextColor    = $c['navbar_text_color'] ?? '#ffffff';
        $navbarBorderColor  = !empty($c['navbar_border_color']) ? $c['navbar_border_color'] : $accent;
        $sidebarBg          = $c['sidebar_bg'] ?? '#0A1428';
        $sidebarTextColor   = $c['sidebar_text_color'] ?? '#cbd5e1';
        $sidebarActiveColor = $c['sidebar_active_color'] ?? '#38bdf8';
        $sidebarActiveBg    = $c['sidebar_active_bg'] ?? 'rgba(255, 255, 255, 0.08)';
        $bodyBg             = $c['body_bg'] ?? '#f8fafc';
        $cardBg             = $c['card_bg'] ?? '#ffffff';
        $sidebarLogoW       = (int)($c['sidebar_logo_width'] ?? 140);
        $sidebarLogoH       = (int)($c['sidebar_logo_height'] ?? 36);
        $logoShape          = $c['logo_shape'] ?? 'standard';
        $logoFit            = $c['logo_fit'] ?? 'contain';
        $font               = $c['font_family'] ?? 'Plus Jakarta Sans';
        $customCss          = $c['custom_css'] ?? '';

        $shapeCss = "";
        if ($logoShape === 'rounded') {
            $shapeCss = "border-radius: 8px !important;";
        } elseif ($logoShape === 'circle') {
            $shapeCss = "border-radius: 50% !important;";
        } elseif ($logoShape === 'pill') {
            $shapeCss = "border-radius: 9999px !important;";
        } elseif ($logoShape === 'bordered') {
            $shapeCss = "border: 1.5px solid rgba(255, 255, 255, 0.25) !important; padding: 4px !important; border-radius: 8px !important; background: rgba(255,255,255,0.05) !important;";
        } elseif ($logoShape === 'glow') {
            $shapeCss = "filter: drop-shadow(0 0 8px rgba(56, 189, 248, 0.5)) !important;";
        }

        return "
/* BrandKitSlater - Global System Theme */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap');

:root {
    --bks-primary: {$primary};
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
    --tblr-primary: {$primary} !important;
    --tblr-font-sans-serif: '{$font}', -apple-system, BlinkMacSystemFont, sans-serif !important;
}

body, .page {
    font-family: var(--tblr-font-sans-serif) !important;
    background-color: {$bodyBg} !important;
}

/* 1. BARRA SUPERIOR (HEADER NAVBAR) */
header.navbar, .navbar-expand-md, #header {
    background: {$navbarBg} !important;
    border-bottom: 3px solid {$navbarBorderColor} !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12) !important;
}

/* Textos e ícones na barra superior */
header.navbar > .container-fluid > .navbar-nav > .nav-item > .nav-link,
header.navbar > .container-xl > .navbar-nav > .nav-item > .nav-link,
header.navbar .navbar-nav > .nav-item > .nav-link,
header.navbar .navbar-brand,
header.navbar .nav-link-title,
header.navbar .nav-link-icon,
header.navbar > .container-fluid > * > a:not(.dropdown-item),
header.navbar > .container-xl > * > a:not(.dropdown-item) {
    color: {$navbarTextColor} !important;
}

/* 2. BARRA LATERAL (SIDEBAR VERTICAL) */
.navbar-vertical,
aside.navbar-vertical,
.navbar-vertical.navbar-expand-lg {
    background: {$sidebarBg} !important;
    border-right: 1px solid rgba(0, 0, 0, 0.12) !important;
}

.navbar-vertical .nav-link {
    color: {$sidebarTextColor} !important;
    border-radius: 8px !important;
    margin: 2px 8px !important;
    transition: all 0.2s ease !important;
}

.navbar-vertical .nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.08) !important;
}

.navbar-vertical .nav-link.active,
.navbar-vertical .nav-item.active > .nav-link {
    color: {$sidebarActiveColor} !important;
    background: {$sidebarActiveBg} !important;
    border-left: 4px solid {$sidebarActiveColor} !important;
    font-weight: 700 !important;
}

/* Logotipo Customizado na Barra Lateral */
.brandkit-sidebar-logo {
    max-width: {$sidebarLogoW}px !important;
    max-height: {$sidebarLogoH}px !important;
    object-fit: {$logoFit} !important;
    transition: all 0.2s ease !important;
    {$shapeCss}
}

/* 3. MENUS SUSPENSOS E DROPDOWNS */
.dropdown-menu,
header.navbar .dropdown-menu {
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
    border-radius: 10px !important;
    padding: 0.5rem !important;
}

.dropdown-menu a,
.dropdown-menu .dropdown-item,
header.navbar .dropdown-menu a,
header.navbar .dropdown-menu .dropdown-item,
header.navbar .dropdown-item,
header.navbar .dropdown-header {
    color: #1e293b !important;
    font-weight: 500 !important;
    font-size: 0.875rem !important;
    border-radius: 6px !important;
    padding: 0.5rem 0.85rem !important;
    transition: all 0.15s ease !important;
    display: flex !important;
    align-items: center !important;
}

.dropdown-menu .dropdown-item:hover,
header.navbar .dropdown-menu .dropdown-item:hover,
.dropdown-menu a:hover {
    color: {$primary} !important;
    background-color: #f1f5f9 !important;
}

.dropdown-menu .dropdown-item.active,
header.navbar .dropdown-menu .dropdown-item.active {
    color: #ffffff !important;
    background-color: {$primary} !important;
}

/* 4. BOTÕES PRIMÁRIOS E AÇÕES */
.btn-primary,
.btn-primary:link,
.btn-primary:visited,
button.btn-primary,
a.btn-primary,
input[type=\"submit\"].btn-primary {
    --tblr-btn-color: #ffffff !important;
    --tblr-btn-hover-color: #ffffff !important;
    --tblr-btn-active-color: #ffffff !important;
    background-color: {$primary} !important;
    border-color: {$primary} !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active {
    --tblr-btn-hover-color: #ffffff !important;
    background-color: {$primaryHover} !important;
    border-color: {$primaryHover} !important;
    color: #ffffff !important;
    transform: translateY(-1px) !important;
}

.btn-primary *,
.btn-primary i,
.btn-primary svg,
.btn-primary span {
    color: #ffffff !important;
    fill: #ffffff !important;
}

/* Botões com Outline Primário */
.btn-outline-primary {
    color: {$primary} !important;
    border-color: {$primary} !important;
}

.btn-outline-primary:hover,
.btn-outline-primary:focus,
.btn-outline-primary:active {
    background-color: {$primary} !important;
    border-color: {$primary} !important;
    color: #ffffff !important;
}

/* Badges e Destaques */
.badge.bg-primary {
    background-color: {$primary} !important;
}

.badge.bg-danger {
    background-color: {$accent} !important;
}

/* Cards do Sistema */
.card, .card-md {
    background-color: {$cardBg} !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.1) !important;
}

{$customCss}
";
    }
}
