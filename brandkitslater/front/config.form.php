<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater v2.2 - Estúdio de Identidade Visual & Integração GLPI
 * Suporte Dedicado a Menu Inteiro (Expandido), Menu Recolhido (70px),
 * Tokens Nativos GLPI 11 e Simulador Interativo em Tempo Real
 * -----------------------------------------------------------------------------
 */

try {
    if (!class_exists('Session') && file_exists(__DIR__ . '/../../../inc/includes.php')) {
        include_once __DIR__ . '/../../../inc/includes.php';
    }

    if (class_exists('Session')) {
        Session::checkRight("config", UPDATE);
    }

    require_once __DIR__ . '/../inc/config.class.php';
    require_once __DIR__ . '/../inc/cssgenerator.class.php';

    $pluginWebDir = (class_exists('Plugin') && method_exists('Plugin', 'getWebDir'))
        ? Plugin::getWebDir('brandkitslater')
        : '/plugins/brandkitslater';

    if (isset($_POST['save_brandkit'])) {
        PluginBrandkitslaterConfig::saveConfig($_POST, $_FILES);
        Html::redirect($pluginWebDir . '/front/config.form.php?saved=1');
    }

    $c = PluginBrandkitslaterConfig::getConfig();

    Html::header("BrandKit Studio", $pluginWebDir . '/front/config.form.php', "config", "pluginbrandkitslaterconfig");

    $cacheBust = time();
    echo '<link rel="stylesheet" type="text/css" href="' . $pluginWebDir . '/css/brandkit_admin.css?v=' . $cacheBust . '">';
    echo '<script src="' . $pluginWebDir . '/js/brandkit_preview.js?v=' . $cacheBust . '"></script>';

    if (isset($_GET['saved'])) {
        echo '<div style="margin: 1rem 2rem 0 2rem; background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; padding: 0.95rem 1.4rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(16,185,129,0.12);">';
        echo '<span>✅ Configurações salvas! Logos (Menu Inteiro & Recolhido), CSS e tokens do GLPI recompilados instantaneamente.</span>';
        echo '</div>';
    }

    $effectiveSidebarLogo = !empty(trim($c['sidebar_logo_url'] ?? '')) ? $c['sidebar_logo_url'] : $c['logo_url'];
    $effectiveCollapsedLogo = !empty(trim($c['collapsed_logo_url'] ?? '')) ? $c['collapsed_logo_url'] : '/plugins/brandkitslater/pics/logo_collapsed.svg';
?>

<div class="brandkit-studio-container" style="padding: 1.5rem 2rem;">

    <!-- Cabeçalho Principal do BrandKit Studio -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; background: linear-gradient(135deg, #0A1428 0%, #132342 100%); padding: 1.4rem 1.85rem; border-radius: 16px; color: #ffffff; box-shadow: 0 12px 28px rgba(10, 20, 40, 0.2); border-left: 6px solid #6366f1; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-size: 1.45rem; font-weight: 800; letter-spacing: -0.02em;">BrandKitSlater Studio</span>
                <span style="font-size: 11px; background: rgba(56, 189, 248, 0.18); color: #38bdf8; padding: 3px 9px; border-radius: 999px; font-weight: 700; border: 1px solid rgba(56, 189, 248, 0.4);">v2.2 Community Lite</span>
                <span style="font-size: 11px; background: rgba(16, 185, 129, 0.16); color: #34d399; padding: 3px 9px; border-radius: 999px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.35);">Menu Inteiro + Recolhido</span>
            </div>
            <p style="margin: 5px 0 0 0; font-size: 0.84rem; color: #94a3b8;">Integração nativa com GLPI 10 & 11: controle independente para ícone de menu recolhido (70px), menu aberto, tela de login e tokens Tabler.</p>
        </div>
        <div style="display: flex; gap: 0.45rem; flex-wrap: wrap;">
            <button type="button" onclick="loadBrandkitColorPreset('slate')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer;">🏢 Slate</button>
            <button type="button" onclick="loadBrandkitColorPreset('clean')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer;">☀️ Clean</button>
            <button type="button" onclick="loadBrandkitColorPreset('emerald')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer;">🌿 Emerald</button>
            <button type="button" onclick="loadBrandkitPreset('cyber_dark')" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer;">🌌 Midnight</button>
        </div>
    </div>

    <!-- Navegação Rápida por Seções do Studio -->
    <div class="brandkit-section-nav" role="tablist" aria-label="Categorias de Personalização">
        <button type="button" class="brandkit-section-pill active" data-section-filter="all">✨ Todas as Seções</button>
        <button type="button" class="brandkit-section-pill" data-section-filter="logos" data-jump-tab="internal">🧭 Menu Inteiro & Recolhido</button>
        <button type="button" class="brandkit-section-pill" data-section-filter="colors" data-jump-tab="internal">🎨 Cores & Layout GLPI</button>
        <button type="button" class="brandkit-section-pill" data-section-filter="login" data-jump-tab="login">🚪 Tela de Login</button>
        <button type="button" class="brandkit-section-pill" data-section-filter="texts">🔤 Textos, Aba & CSS</button>
    </div>

    <!-- Área de Trabalho Lado a Lado -->
    <div class="brandkit-studio-wrapper">

        <!-- Coluna Esquerda: Controles -->
        <div class="brandkit-controls-col">
            <form method="post" action="<?php echo htmlspecialchars($pluginWebDir); ?>/front/config.form.php" enctype="multipart/form-data" id="brandkit-config-form">
                <input type="hidden" name="save_brandkit" value="1">
                <input type="hidden" name="preset" id="bks_preset_input" value="<?php echo htmlspecialchars($c['preset']); ?>">
                <?php
                if (class_exists('Session') && method_exists('Session', 'getNewCSRFToken')) {
                    echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';
                }
                ?>

                <!-- ================================================================ -->
                <!-- SEÇÃO 1: LOGOS INDEPENDENTES (MENU INTEIRO vs MENU RECOLHIDO)    -->
                <!-- ================================================================ -->
                <div class="brandkit-card" data-bks-section="logos">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.65rem; flex-wrap: wrap; gap: 10px;">
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>🧭 Ícones do Menu GLPI (Inteiro & Recolhido) e Identidade</span>
                        </h3>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #0f172a; background: #f0f9ff; padding: 6px 12px; border-radius: 8px; border: 1.5px solid #bae6fd;">
                            <input type="checkbox" name="enable_custom_logos" id="bks_enable_custom_logos" value="1" <?php echo (!isset($c['enable_custom_logos']) || !empty($c['enable_custom_logos']) ? 'checked' : ''); ?> style="width: 16px; height: 16px; accent-color: #003876;">
                            <span>Aplicar logos no menu interno do GLPI</span>
                        </label>
                    </div>
                    <p style="font-size: 0.8rem; color: #64748b; margin: 0 0 1.25rem 0;">
                        Configure imagens e tamanhos independentes para quando a barra lateral estiver <strong>aberta (inteira)</strong> e quando o usuário clicar em <strong>« Recolher menu (70px)</strong>. Funciona mesmo com o tema de cores nativo do GLPI!
                    </p>

                    <!-- Grid Duplo: Menu Inteiro (Expandido) vs. Menu Recolhido (70px) -->
                    <div class="brandkit-dual-menu-grid">

                        <!-- SLOT A: MENU INTEIRO (EXPANDIDO) -->
                        <div class="brandkit-menu-slot">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.82rem; font-weight: 800; color: #0f172a;">📖 Logo do Menu Inteiro (Aberto)</span>
                                <span style="font-size: 0.7rem; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 2px 7px; border-radius: 999px;">Horizontal</span>
                            </div>

                            <!-- Palco em Tempo Real do Menu Aberto -->
                            <div class="brandkit-menu-stage" id="bks-stage-expanded">
                                <span class="brandkit-stage-tag">Menu Expandido</span>
                                <img id="bks-stage-expanded-img" src="<?php echo htmlspecialchars($effectiveSidebarLogo); ?>" alt="Preview Menu Inteiro" style="max-width: 145px; max-height: 40px; object-fit: contain; transition: all 0.2s ease;">
                            </div>

                            <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                <label class="brandkit-upload-btn">
                                    <span>📤 Enviar Logo Menu</span>
                                    <input type="file" name="sidebar_logo_file" id="bks_sidebar_logo_file" accept="image/png,image/svg+xml,image/jpeg,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('sidebar_logo_file_hint').innerText = '📁 ' + this.files[0].name; const r = new FileReader(); r.onload = function(e){ document.getElementById('bks_sidebar_logo_url').value = e.target.result; document.getElementById('bks_sidebar_logo_url').dispatchEvent(new Event('input')); }; r.readAsDataURL(this.files[0]); }">
                                </label>
                                <button type="button" class="brandkit-mini-btn" title="Herda automaticamente o mesmo logotipo da tela de login" onclick="document.getElementById('bks_sidebar_logo_url').value=''; document.getElementById('bks_sidebar_logo_url').dispatchEvent(new Event('input'));">🔄 Usar Principal</button>
                                <button type="button" class="brandkit-mini-btn" title="Usa versão vetorial clara ideal para sidebars escuras" onclick="document.getElementById('bks_sidebar_logo_url').value='/plugins/brandkitslater/pics/logo_sidebar_light.svg'; document.getElementById('bks_sidebar_logo_url').dispatchEvent(new Event('input'));">✨ Versão Clara</button>
                            </div>
                            <span id="sidebar_logo_file_hint" style="font-size: 0.73rem; color: #64748b;">Deixe vazio para herdar o Logo Principal, ou envie versão horizontal clara</span>

                            <input type="text" name="sidebar_logo_url" id="bks_sidebar_logo_url" placeholder="Vazio = usa o Logo Principal automaticamente" value="<?php echo htmlspecialchars($c['sidebar_logo_url'] ?? ''); ?>" style="width: 100%; padding: 0.5rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem;">

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 3px;">Largura Máx.</label>
                                    <div class="brandkit-slider-group">
                                        <input type="range" name="sidebar_logo_width" id="bks_sidebar_logo_width" min="60" max="240" value="<?php echo htmlspecialchars($c['sidebar_logo_width']); ?>">
                                        <span class="brandkit-slider-value" data-target="bks_sidebar_logo_width" data-unit="px"><?php echo htmlspecialchars($c['sidebar_logo_width']); ?>px</span>
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 3px;">Altura Máx.</label>
                                    <div class="brandkit-slider-group">
                                        <input type="range" name="sidebar_logo_height" id="bks_sidebar_logo_height" min="22" max="80" value="<?php echo htmlspecialchars($c['sidebar_logo_height']); ?>">
                                        <span class="brandkit-slider-value" data-target="bks_sidebar_logo_height" data-unit="px"><?php echo htmlspecialchars($c['sidebar_logo_height']); ?>px</span>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; gap: 0.75rem; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                                <select name="sidebar_logo_shape" id="bks_sidebar_logo_shape" style="flex: 1; padding: 0.45rem 0.65rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                    <?php $sShape = $c['sidebar_logo_shape'] ?? 'standard'; ?>
                                    <option value="standard" <?php echo ($sShape === 'standard' ? 'selected' : ''); ?>>Formato: Natural / Padrão</option>
                                    <option value="rounded" <?php echo ($sShape === 'rounded' ? 'selected' : ''); ?>>Formato: Cantos Arredondados</option>
                                    <option value="glass_badge" <?php echo ($sShape === 'glass_badge' ? 'selected' : ''); ?>>Formato: Moldura Glass Translúcida</option>
                                    <option value="bordered" <?php echo ($sShape === 'bordered' ? 'selected' : ''); ?>>Formato: Placa Branca com Borda</option>
                                    <option value="glow" <?php echo ($sShape === 'glow' ? 'selected' : ''); ?>>Formato: Brilho Neon Suave</option>
                                </select>
                                <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.76rem; font-weight: 700; color: #334155; cursor: pointer;" title="Adiciona fundo claro atrás de logos com letras escuras">
                                    <input type="checkbox" name="sidebar_logo_bg_pill" id="bks_sidebar_logo_bg_pill" value="1" <?php echo (!empty($c['sidebar_logo_bg_pill']) ? 'checked' : ''); ?> style="accent-color: #003876;">
                                    <span>Fundo Claro Contraste</span>
                                </label>
                            </div>
                        </div>

                        <!-- SLOT B: ÍCONE DO MENU RECOLHIDO (70PX) -->
                        <div class="brandkit-menu-slot">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.82rem; font-weight: 800; color: #0f172a;">📐 Ícone do Menu Recolhido</span>
                                <span style="font-size: 0.7rem; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 2px 7px; border-radius: 999px;">70px Compacto</span>
                            </div>

                            <!-- Palco Escala Real 70px -->
                            <div class="brandkit-menu-stage" id="bks-stage-collapsed">
                                <span class="brandkit-stage-tag">Trilho 70px</span>
                                <div class="brandkit-menu-stage-rail">
                                    <img id="bks-stage-collapsed-img" src="<?php echo htmlspecialchars($effectiveCollapsedLogo); ?>" alt="Preview Ícone Recolhido" style="width: 38px; height: 38px; object-fit: contain; transition: all 0.2s ease;">
                                </div>
                            </div>

                            <div style="display: flex; gap: 6px; flex-wrap: wrap; align-items: center;">
                                <label class="brandkit-upload-btn">
                                    <span>📤 Enviar Ícone</span>
                                    <input type="file" name="collapsed_logo_file" id="bks_collapsed_logo_file" accept="image/png,image/svg+xml,.ico,image/jpeg,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('collapsed_logo_file_hint').innerText = '📁 ' + this.files[0].name; const r = new FileReader(); r.onload = function(e){ document.getElementById('bks_collapsed_logo_url').value = e.target.result; document.getElementById('bks_collapsed_logo_url').dispatchEvent(new Event('input')); }; r.readAsDataURL(this.files[0]); }">
                                </label>
                                <button type="button" class="brandkit-mini-btn" onclick="document.getElementById('bks_collapsed_logo_url').value='/plugins/brandkitslater/pics/logo_collapsed.svg'; document.getElementById('bks_collapsed_logo_url').dispatchEvent(new Event('input'));">🛡️ Ícone Padrão</button>
                                <button type="button" class="brandkit-mini-btn" onclick="document.getElementById('bks_collapsed_logo_url').value=document.getElementById('bks_favicon_url').value; document.getElementById('bks_collapsed_logo_url').dispatchEvent(new Event('input'));">🌐 Usar Favicon</button>
                            </div>
                            <span id="collapsed_logo_file_hint" style="font-size: 0.73rem; color: #64748b;">Ideal: monograma quadrado (1:1), brasão ou símbolo sem texto longo</span>

                            <input type="text" name="collapsed_logo_url" id="bks_collapsed_logo_url" placeholder="/plugins/brandkitslater/pics/logo_collapsed.svg" value="<?php echo htmlspecialchars($effectiveCollapsedLogo); ?>" style="width: 100%; padding: 0.5rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem;">

                            <div>
                                <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 3px;">Tamanho do Ícone Recolhido (22px – 54px)</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="collapsed_logo_size" id="bks_collapsed_logo_size" min="22" max="54" value="<?php echo htmlspecialchars($c['collapsed_logo_size'] ?? 38); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_collapsed_logo_size" data-unit="px"><?php echo htmlspecialchars($c['collapsed_logo_size'] ?? 38); ?>px</span>
                                </div>
                            </div>

                            <div>
                                <select name="collapsed_logo_shape" id="bks_collapsed_logo_shape" style="width: 100%; padding: 0.45rem 0.65rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                    <?php $cShape = $c['collapsed_logo_shape'] ?? 'rounded'; ?>
                                    <option value="rounded" <?php echo ($cShape === 'rounded' ? 'selected' : ''); ?>>Estilo Ícone: Arredondado Suave (10px)</option>
                                    <option value="glass_badge" <?php echo ($cShape === 'glass_badge' ? 'selected' : ''); ?>>Estilo Ícone: Badge Glass Translúcido ⭐</option>
                                    <option value="circle" <?php echo ($cShape === 'circle' ? 'selected' : ''); ?>>Estilo Ícone: Circular Perfeito (1:1)</option>
                                    <option value="bordered" <?php echo ($cShape === 'bordered' ? 'selected' : ''); ?>>Estilo Ícone: Fundo Branco c/ Borda</option>
                                    <option value="glow" <?php echo ($cShape === 'glow' ? 'selected' : ''); ?>>Estilo Ícone: Glow Luminoso</option>
                                    <option value="standard" <?php echo ($cShape === 'standard' ? 'selected' : ''); ?>>Estilo Ícone: Sem Moldura (Natural)</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Logo Principal (Tela de Login & Herança) + Favicon -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                            <label style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                                🏷️ Logo Principal / Card de Login
                            </label>
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.65rem; flex-wrap: wrap;">
                                <label class="brandkit-upload-btn">
                                    <span>📤 Enviar Logo</span>
                                    <input type="file" name="logo_file" id="bks_logo_file" accept="image/png,image/svg+xml,image/jpeg,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('logo_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_logo_url').value = e.target.result; document.getElementById('bks_logo_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                                </label>
                                <button type="button" class="brandkit-mini-btn" onclick="document.getElementById('bks_logo_url').value='/plugins/brandkitslater/pics/logo.png'; document.getElementById('bks_logo_url').dispatchEvent(new Event('input'));">🛡️ Padrão Lite</button>
                                <span id="logo_file_name" style="font-size: 0.74rem; color: #64748b;">PNG, SVG ou WebP</span>
                            </div>
                            <input type="text" name="logo_url" id="bks_logo_url" placeholder="/plugins/brandkitslater/pics/logo.png" value="<?php echo htmlspecialchars($c['logo_url']); ?>" style="width: 100%; padding: 0.5rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; margin-bottom: 0.75rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <label style="font-size: 0.7rem; font-weight: 700; color: #475569;">Largura Login</label>
                                    <div class="brandkit-slider-group">
                                        <input type="range" name="login_logo_width" id="bks_login_logo_width" min="80" max="360" value="<?php echo htmlspecialchars($c['login_logo_width']); ?>">
                                        <span class="brandkit-slider-value" data-target="bks_login_logo_width" data-unit="px"><?php echo htmlspecialchars($c['login_logo_width']); ?>px</span>
                                    </div>
                                </div>
                                <div>
                                    <label style="font-size: 0.7rem; font-weight: 700; color: #475569;">Altura Login</label>
                                    <div class="brandkit-slider-group">
                                        <input type="range" name="login_logo_height" id="bks_login_logo_height" min="25" max="120" value="<?php echo htmlspecialchars($c['login_logo_height']); ?>">
                                        <span class="brandkit-slider-value" data-target="bks_login_logo_height" data-unit="px"><?php echo htmlspecialchars($c['login_logo_height']); ?>px</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                            <label style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                                🌐 Favicon da Aba do Navegador
                            </label>
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.65rem; flex-wrap: wrap;">
                                <label class="brandkit-upload-btn">
                                    <span>📤 Enviar Favicon</span>
                                    <input type="file" name="favicon_file" id="bks_favicon_file" accept=".ico,image/png,image/svg+xml" style="display:none;" onchange="if(this.files[0]) { document.getElementById('favicon_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_favicon_url').value = e.target.result; document.getElementById('bks_favicon_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                                </label>
                                <button type="button" class="brandkit-mini-btn" onclick="document.getElementById('bks_favicon_url').value='/plugins/brandkitslater/pics/favicon.ico'; document.getElementById('bks_favicon_url').dispatchEvent(new Event('input'));">🛡️ Padrão Lite</button>
                                <span id="favicon_file_name" style="font-size: 0.74rem; color: #64748b;">.ICO, .PNG ou .SVG</span>
                            </div>
                            <input type="text" name="favicon_url" id="bks_favicon_url" placeholder="/plugins/brandkitslater/pics/favicon.ico" value="<?php echo htmlspecialchars($c['favicon_url']); ?>" style="width: 100%; padding: 0.5rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; margin-bottom: 0.75rem;">

                            <label style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Proporção Global das Imagens (Object Fit)</label>
                            <select name="logo_fit" id="bks_logo_fit" style="width: 100%; padding: 0.48rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; font-weight: 600;">
                                <option value="contain" <?php echo ($c['logo_fit'] === 'contain' ? 'selected' : ''); ?>>Conter Proporcional (Sem cortes - Recomendado)</option>
                                <option value="cover" <?php echo ($c['logo_fit'] === 'cover' ? 'selected' : ''); ?>>Preencher Quadro (Cover)</option>
                                <option value="scale-down" <?php echo ($c['logo_fit'] === 'scale-down' ? 'selected' : ''); ?>>Reduzir se Maior (Scale-Down)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Seletor Visual do Formato do Logo de Login -->
                    <div>
                        <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Formato Visual do Logotipo no Card de Login</label>
                        <div class="brandkit-shape-grid">
                            <?php
                            $loginShapes = [
                                'standard'    => ['label' => 'Padrão',       'demo' => 'LOGO'],
                                'rounded'     => ['label' => 'Arredondado',  'demo' => 'LOGO'],
                                'circle'      => ['label' => 'Circular 1:1', 'demo' => '●'],
                                'glass_badge' => ['label' => 'Badge Glass',  'demo' => '❖'],
                                'bordered'    => ['label' => 'Com Moldura',  'demo' => 'LOGO'],
                                'glow'        => ['label' => 'Brilho Suave', 'demo' => 'LOGO'],
                            ];
                            foreach ($loginShapes as $sKey => $sMeta):
                            ?>
                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === $sKey ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_<?php echo $sKey; ?>" value="<?php echo $sKey; ?>" style="display:none;" <?php echo ($c['logo_shape'] === $sKey ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo <?php echo $sKey; ?>"><?php echo $sMeta['demo']; ?></div>
                                <span style="font-size: 0.75rem; font-weight: 700; color: #1e293b;"><?php echo $sMeta['label']; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ================================================================ -->
                <!-- SEÇÃO 2: CORES & INTEGRAÇÃO PROFUNDA DE LAYOUT COM O GLPI 11     -->
                <!-- ================================================================ -->
                <div class="brandkit-card" data-bks-section="colors">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 10px;">
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>🎨 Cores, Barra Lateral & Componentes Nativos GLPI</span>
                        </h3>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.8rem; font-weight: 700; color: #334155; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1;">
                            <input type="checkbox" name="enable_internal_theme" id="bks_enable_internal_theme" value="1" <?php echo (!empty($c['enable_internal_theme']) ? 'checked' : ''); ?> style="width: 16px; height: 16px; accent-color: #003876;">
                            <span>Ativar Tema de Cores no Painel Interno</span>
                        </label>
                    </div>
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 1.15rem;">
                        Sincronizado diretamente com as variáveis nativas <code>--glpi-mainmenu-*</code> e <code>--tblr-primary-rgb</code> do GLPI 11 (incluindo submenus flutuantes do menu recolhido).
                    </p>

                    <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Paletas Prontas de 1 Clique</label>
                    <div class="brandkit-color-presets">
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('slate')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#1e293b;"></span><span class="brandkit-preset-dot" style="background:#6366f1;"></span><span class="brandkit-preset-dot" style="background:#0f172a;"></span></span>
                            <span>🏢 Corporate Slate</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('clean')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#2563eb;"></span><span class="brandkit-preset-dot" style="background:#f59e0b;"></span><span class="brandkit-preset-dot" style="background:#ffffff;"></span></span>
                            <span>☀️ Clean Light</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('emerald')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#059669;"></span><span class="brandkit-preset-dot" style="background:#10b981;"></span><span class="brandkit-preset-dot" style="background:#064e3b;"></span></span>
                            <span>🌿 Emerald</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('bordeaux')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#881337;"></span><span class="brandkit-preset-dot" style="background:#d97706;"></span><span class="brandkit-preset-dot" style="background:#4c0519;"></span></span>
                            <span>🍷 Bordeaux</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('midnight')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#0284c7;"></span><span class="brandkit-preset-dot" style="background:#38bdf8;"></span><span class="brandkit-preset-dot" style="background:#020617;"></span></span>
                            <span>🌌 Midnight</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('classic')">
                            <span class="brandkit-preset-dots"><span class="brandkit-preset-dot" style="background:#2f3f64;"></span><span class="brandkit-preset-dot" style="background:#fec95c;"></span><span class="brandkit-preset-dot" style="background:#f4f6fa;"></span></span>
                            <span>🦊 GLPI Nativo</span>
                        </button>
                    </div>

                    <!-- Cores Principais -->
                    <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.15rem;">
                        <h4 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 800; color: #1e293b;">🎯 Cores Primárias e Ações (--tblr-primary)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Cor Primária</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="primary_color" id="bks_primary_color" value="<?php echo htmlspecialchars($c['primary_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_primary_color_text" value="<?php echo htmlspecialchars($c['primary_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Hover Primário</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="primary_hover" id="bks_primary_hover" value="<?php echo htmlspecialchars($c['primary_hover']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_primary_hover_text" value="<?php echo htmlspecialchars($c['primary_hover']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Destaque / Accent</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="accent_color" id="bks_accent_color" value="<?php echo htmlspecialchars($c['accent_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_accent_color_text" value="<?php echo htmlspecialchars($c['accent_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barra Lateral (Sidebar) + Ergonomia -->
                    <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.15rem;">
                        <h4 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 800; color: #1e293b;">🧭 Menu Lateral do GLPI (Cores, Largura & Estilo dos Itens)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Fundo da Sidebar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_bg" id="bks_sidebar_bg" value="<?php echo htmlspecialchars($c['sidebar_bg']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_bg_text" value="<?php echo htmlspecialchars($c['sidebar_bg']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Texto e Ícones</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_text_color" id="bks_sidebar_text_color" value="<?php echo htmlspecialchars($c['sidebar_text_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_text_color_text" value="<?php echo htmlspecialchars($c['sidebar_text_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Item Ativo</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_active_color" id="bks_sidebar_active_color" value="<?php echo htmlspecialchars($c['sidebar_active_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_active_color_text" value="<?php echo htmlspecialchars($c['sidebar_active_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Fundo Item Ativo</label>
                                <input type="text" name="sidebar_active_bg" id="bks_sidebar_active_bg" value="<?php echo htmlspecialchars($c['sidebar_active_bg']); ?>" placeholder="rgba(255,255,255,0.08)" style="width:100%; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Largura do Menu Aberto (px)</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="sidebar_width" id="bks_sidebar_width" min="200" max="300" value="<?php echo htmlspecialchars($c['sidebar_width'] ?? 240); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_sidebar_width" data-unit="px"><?php echo htmlspecialchars($c['sidebar_width'] ?? 240); ?>px</span>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Design dos Links do Menu</label>
                                <?php $itemStyle = $c['sidebar_item_style'] ?? 'pill'; ?>
                                <select name="sidebar_item_style" id="bks_sidebar_item_style" style="width: 100%; padding: 0.48rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-weight: 600;">
                                    <option value="pill" <?php echo ($itemStyle === 'pill' ? 'selected' : ''); ?>>Pílula SaaS Arredondada ⭐</option>
                                    <option value="native" <?php echo ($itemStyle === 'native' ? 'selected' : ''); ?>>Clássico Nativo GLPI (Full-Width)</option>
                                    <option value="glass" <?php echo ($itemStyle === 'glass' ? 'selected' : ''); ?>>Glassmorphism Translúcido</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Barra Superior (Header Navbar) -->
                    <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.15rem;">
                        <h4 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 800; color: #1e293b;">🔝 Barra Superior (Breadcrumbs, Busca & Perfil)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(185px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Fundo da Navbar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_bg" id="bks_navbar_bg" value="<?php echo htmlspecialchars($c['navbar_bg']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_bg_text" value="<?php echo htmlspecialchars($c['navbar_bg']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Texto e Ícones</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_text_color" id="bks_navbar_text_color" value="<?php echo htmlspecialchars($c['navbar_text_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_text_color_text" value="<?php echo htmlspecialchars($c['navbar_text_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Linha Inferior</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_border_color" id="bks_navbar_border_color" value="<?php echo htmlspecialchars($c['navbar_border_color']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_border_color_text" value="<?php echo htmlspecialchars($c['navbar_border_color']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Superfícies, Raio de Bordas & Acabamento dos Cards do GLPI -->
                    <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <h4 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 800; color: #1e293b;">📄 Superfícies, Arredondamento Global & Cartões de Chamados</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Fundo da Página (Body)</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="body_bg" id="bks_body_bg" value="<?php echo htmlspecialchars($c['body_bg']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_body_bg_text" value="<?php echo htmlspecialchars($c['body_bg']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Fundo dos Cartões</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="card_bg" id="bks_card_bg" value="<?php echo htmlspecialchars($c['card_bg']); ?>" style="width: 40px; height: 36px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_card_bg_text" value="<?php echo htmlspecialchars($c['card_bg']); ?>" style="flex:1; padding: 0.42rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem;">
                                </div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: end;">
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Raio Global da UI (--tblr-border-radius)</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="ui_border_radius" id="bks_ui_border_radius" min="0" max="20" value="<?php echo htmlspecialchars($c['ui_border_radius'] ?? 10); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_ui_border_radius" data-unit="px"><?php echo htmlspecialchars($c['ui_border_radius'] ?? 10); ?>px</span>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Acabamento dos Painéis</label>
                                <?php $icStyle = $c['internal_card_style'] ?? 'elevated'; ?>
                                <select name="internal_card_style" id="bks_internal_card_style" style="width: 100%; padding: 0.48rem 0.7rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-weight: 600;">
                                    <option value="elevated" <?php echo ($icStyle === 'elevated' ? 'selected' : ''); ?>>Elevado com Sombra Suave</option>
                                    <option value="accent_top" <?php echo ($icStyle === 'accent_top' ? 'selected' : ''); ?>>Borda Superior Institucional</option>
                                    <option value="native" <?php echo ($icStyle === 'native' ? 'selected' : ''); ?>>Plano Clássico GLPI</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-top: 0.9rem; padding-top: 0.85rem; border-top: 1px solid #e2e8f0;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 600; color: #334155; cursor: pointer;">
                                <input type="checkbox" name="style_scrollbars_selection" id="bks_style_scrollbars_selection" value="1" <?php echo (!isset($c['style_scrollbars_selection']) || !empty($c['style_scrollbars_selection']) ? 'checked' : ''); ?> style="accent-color: #003876;">
                                <span>Harmonizar seleção de texto (<code>::selection</code>), checkboxes e barras de rolagem com a cor primária</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ================================================================ -->
                <!-- SEÇÃO 3: LAYOUT & GLASSMORPHISM DA TELA DE LOGIN                 -->
                <!-- ================================================================ -->
                <div class="brandkit-card" data-bks-section="login">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                        <span>🚪 Layout, Fundo e Glassmorphism da Tela de Login</span>
                    </h3>

                    <div style="margin-bottom: 1.4rem;">
                        <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Posicionamento do Card de Login</label>
                        <div class="brandkit-position-grid">
                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'left' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_left" value="left" style="display:none;" <?php echo ($c['login_position'] === 'left' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: flex-start;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.83rem; color: #1e293b;">Canto Esquerdo</span>
                            </label>
                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'center' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_center" value="center" style="display:none;" <?php echo ($c['login_position'] === 'center' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: center;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.83rem; color: #1e293b;">Centro Clássico</span>
                            </label>
                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'right' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_right" value="right" style="display:none;" <?php echo ($c['login_position'] === 'right' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: flex-end;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.83rem; color: #1e293b;">Canto Direito ⭐</span>
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.4rem;">
                        <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Material Visual do Card</label>
                        <div style="display: flex; gap: 1.25rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_glass" value="glassmorphism" <?php echo ($c['card_style'] === 'glassmorphism' ? 'checked' : ''); ?> style="accent-color:#003876;"> 💎 Glassmorphism 2.0
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_flat" value="flat" <?php echo ($c['card_style'] === 'flat' ? 'checked' : ''); ?> style="accent-color:#003876;"> 🧊 Clean Sólido
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.86rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_bordered" value="bordered" <?php echo ($c['card_style'] === 'bordered' ? 'checked' : ''); ?> style="accent-color:#003876;"> 📦 Borda Destacada
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Largura do Card</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_width" id="bks_card_width" min="360" max="620" value="<?php echo htmlspecialchars($c['card_width']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_width" data-unit="px"><?php echo htmlspecialchars($c['card_width']); ?>px</span>
                            </div>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Arredondamento</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_radius" id="bks_card_radius" min="0" max="32" value="<?php echo htmlspecialchars($c['card_radius']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_radius" data-unit="px"><?php echo htmlspecialchars($c['card_radius']); ?>px</span>
                            </div>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Desfoque (Blur)</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_blur" id="bks_card_blur" min="0" max="40" value="<?php echo htmlspecialchars($c['card_blur']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_blur" data-unit="px"><?php echo htmlspecialchars($c['card_blur']); ?>px</span>
                            </div>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Opacidade do Card</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_opacity" id="bks_card_opacity" min="50" max="100" value="<?php echo htmlspecialchars($c['card_opacity']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_opacity" data-unit="%"><?php echo htmlspecialchars($c['card_opacity']); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem; background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <label style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                            🖼️ Papel de Parede do Login (Wallpaper)
                        </label>
                        <div style="display: flex; gap: 0.65rem; align-items: center; margin-bottom: 0.65rem; flex-wrap: wrap;">
                            <label class="brandkit-upload-btn">
                                <span>📤 Enviar Wallpaper</span>
                                <input type="file" name="bg_image_file" id="bks_bg_image_file" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('bg_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_bg_image_url').value = e.target.result; document.getElementById('bks_bg_image_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                            </label>
                            <button type="button" class="brandkit-mini-btn" onclick="document.getElementById('bks_bg_image_url').value='/plugins/brandkitslater/pics/login_bg.jpg'; document.getElementById('bks_bg_image_url').dispatchEvent(new Event('input'));">🌌 Wallpaper Padrão</button>
                            <span id="bg_file_name" style="font-size: 0.75rem; color: #64748b;">JPG, PNG ou WebP em alta definição</span>
                        </div>
                        <input type="text" name="bg_image_url" id="bks_bg_image_url" value="<?php echo htmlspecialchars($c['bg_image_url']); ?>" placeholder="/plugins/brandkitslater/pics/login_bg.jpg" style="width: 100%; padding: 0.5rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; margin-bottom: 0.85rem;">

                        <label style="font-size: 0.74rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Contraste da Vinheta Escura sobre o Fundo</label>
                        <div class="brandkit-slider-group">
                            <input type="range" name="bg_overlay_opacity" id="bks_bg_overlay_opacity" min="0" max="95" value="<?php echo htmlspecialchars($c['bg_overlay_opacity']); ?>">
                            <span class="brandkit-slider-value" data-target="bks_bg_overlay_opacity" data-unit="%"><?php echo htmlspecialchars($c['bg_overlay_opacity']); ?>%</span>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 1.15rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                            <div>
                                <span style="font-weight: 800; font-size: 0.88rem; color: #0f172a; display: block;">Botão de Auto-Cadastro / Portal Externo no Login</span>
                                <span style="font-size: 0.76rem; color: #64748b;">Exibe botão secundário abaixo do formulário de entrada</span>
                            </div>
                            <input type="checkbox" name="enable_external_register" id="bks_enable_external_register" value="1" <?php echo (!empty($c['enable_external_register']) ? 'checked' : ''); ?> style="width: 18px; height: 18px; accent-color: #003876;">
                        </div>
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 0.75rem;">
                            <div>
                                <label style="font-size: 0.72rem; font-weight: 700; color: #475569; display: block; margin-bottom: 3px;">Texto do Botão</label>
                                <input type="text" name="external_register_text" id="bks_external_register_text" value="<?php echo htmlspecialchars($c['external_register_text'] ?? 'Auto-Cadastro de Usuários Externos'); ?>" style="width: 100%; padding: 0.48rem 0.65rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem;">
                            </div>
                            <div>
                                <label style="font-size: 0.72rem; font-weight: 700; color: #475569; display: block; margin-bottom: 3px;">URL de Destino</label>
                                <input type="text" name="external_register_url" id="bks_external_register_url" value="<?php echo htmlspecialchars($c['external_register_url'] ?? '/cadastro/'); ?>" placeholder="/cadastro/" style="width: 100%; padding: 0.48rem 0.65rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.8rem; font-family: monospace;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================================================================ -->
                <!-- SEÇÃO 4: TEXTOS, TÍTULO DA ABA, TIPOGRAFIA & CSS AVANÇADO        -->
                <!-- ================================================================ -->
                <div class="brandkit-card" data-bks-section="texts">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem;">
                        🔤 Textos Personalizados, Marca da Aba & CSS Avançado
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Família Tipográfica</label>
                            <select name="font_family" id="bks_font_family" style="width: 100%; padding: 0.55rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600; font-size: 0.84rem;">
                                <option value="Plus Jakarta Sans" <?php echo ($c['font_family'] === 'Plus Jakarta Sans' ? 'selected' : ''); ?>>Plus Jakarta Sans (Enterprise)</option>
                                <option value="Inter" <?php echo ($c['font_family'] === 'Inter' ? 'selected' : ''); ?>>Inter (SaaS Moderno)</option>
                                <option value="Montserrat" <?php echo ($c['font_family'] === 'Montserrat' ? 'selected' : ''); ?>>Montserrat (Institucional)</option>
                                <option value="Roboto" <?php echo ($c['font_family'] === 'Roboto' ? 'selected' : ''); ?>>Roboto (Técnica)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Marca na Aba do Navegador (&lt;title&gt;)</label>
                            <input type="text" name="page_title_brand" id="bks_page_title_brand" value="<?php echo htmlspecialchars($c['page_title_brand'] ?? ''); ?>" placeholder="Ex: SuaEmpresa (substitui 'GLPI' na aba)" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Selo Superior do Login (Vazio = Oculta)</label>
                            <input type="text" name="login_badge_text" id="bks_login_badge_text" value="<?php echo htmlspecialchars($c['login_badge_text'] ?? 'Central de Atendimento & Suporte'); ?>" placeholder="Central de Atendimento & Suporte" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Título Principal do Card de Login</label>
                            <input type="text" name="login_title" id="bks_login_title" value="<?php echo htmlspecialchars($c['login_title'] ?? 'Entrar no Sistema'); ?>" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Subtítulo do Card de Login</label>
                            <input type="text" name="login_subtitle" id="bks_login_subtitle" value="<?php echo htmlspecialchars($c['login_subtitle'] ?? 'Acesso corporativo seguro'); ?>" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Texto do Botão Entrar</label>
                            <input type="text" name="login_btn_text" id="bks_login_btn_text" value="<?php echo htmlspecialchars($c['login_btn_text'] ?? 'Acessar Atendimento'); ?>" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">Texto do Rodapé de Segurança</label>
                        <input type="text" name="footer_text" id="bks_footer_text" value="<?php echo htmlspecialchars($c['footer_text']); ?>" style="width: 100%; padding: 0.52rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.84rem;">
                    </div>

                    <div>
                        <label style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 4px;">CSS Customizado Extra (Injetado no Login e Painel)</label>
                        <textarea name="custom_css" id="bks_custom_css" rows="3" placeholder="/* Regras CSS extras opcionais */" style="width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.82rem; resize: vertical;"><?php echo htmlspecialchars($c['custom_css']); ?></textarea>
                    </div>
                </div>

                <!-- Barra Flutuante de Salvamento -->
                <div class="brandkit-save-bar">
                    <div style="font-size: 0.83rem; color: #475569; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                        <span>Compila CSS estático e sincroniza Menu Inteiro + Menu Recolhido imediatamente.</span>
                    </div>
                    <button type="submit" class="brandkit-save-btn">
                        <span>💾 Salvar e Aplicar no GLPI</span>
                    </button>
                </div>

            </form>
        </div>

        <!-- Coluna Direita: Simulador em Tempo Real -->
        <div class="brandkit-preview-col">
            <div class="brandkit-preview-frame" id="bks-preview-frame">

                <div class="brandkit-preview-toolbar">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: flex; gap: 6px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; opacity: 0.85;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; opacity: 0.85;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; opacity: 0.85;"></span>
                        </div>
                        <span style="font-size: 0.76rem; font-weight: 800; letter-spacing: 0.04em; color: #e2e8f0;">LIVE STUDIO</span>
                        <span id="bks-contrast-badge" class="brandkit-contrast-badge">✨ WCAG AAA</span>
                    </div>
                    <div class="brandkit-device-segmented">
                        <button type="button" class="brandkit-device-btn active" data-device="desktop">🖥️ Desktop</button>
                        <button type="button" class="brandkit-device-btn" data-device="tablet">📟 Tablet</button>
                        <button type="button" class="brandkit-device-btn" data-device="mobile">📱 Mobile</button>
                    </div>
                </div>

                <div class="brandkit-preview-tabs">
                    <button type="button" class="brandkit-tab-btn active" data-tab="login">🚪 Tela de Login</button>
                    <button type="button" class="brandkit-tab-btn" data-tab="internal">🧭 Menu GLPI (Inteiro / Recolhido)</button>
                    <button type="button" class="brandkit-tab-btn" data-tab="browser">🌐 Aba & Favicon</button>
                </div>

                <!-- ABA 1: TELA DE LOGIN -->
                <div id="bks-preview-tab-login" class="brandkit-tab-pane" style="display: block;">
                    <div id="bks-preview-container" style="
                        min-height: 530px;
                        background-color: #0A1428;
                        background-image: url('<?php echo htmlspecialchars($c['bg_image_url']); ?>');
                        background-size: cover;
                        background-position: left center;
                        position: relative;
                        display: flex;
                        align-items: center;
                        justify-content: flex-end;
                        padding: 2rem;
                        transition: all 0.3s ease;
                    ">
                        <div id="bks-preview-overlay" style="position: absolute; inset: 0; pointer-events: none;"></div>

                        <div id="bks-preview-card" style="
                            position: relative;
                            z-index: 2;
                            width: 100%;
                            max-width: 380px;
                            box-shadow: 0 20px 45px rgba(0,0,0,0.45);
                            overflow: hidden;
                            transition: all 0.25s ease;
                        ">
                            <div id="bks-preview-accent-bar" style="height: 5px; width: 100%;"></div>

                            <div style="padding: 1.65rem 1.85rem;">
                                <div style="text-align: center; margin-bottom: 1.15rem;">
                                    <div style="margin-bottom: 0.9rem; display: flex; justify-content: center; align-items: center;">
                                        <img id="bks-preview-login-logo" src="<?php echo htmlspecialchars($c['logo_url']); ?>" alt="Logo" style="display: block; max-width: 200px; max-height: 55px; object-fit: contain; transition: all 0.2s ease;">
                                    </div>
                                    <div id="bks-preview-login-badge" style="display: inline-flex; align-items: center; gap: 6px; background: #0A1428; padding: 5px 14px; border-radius: 10px; color: #fff; font-size: 10.5px; font-weight: 700;">
                                        <?php echo htmlspecialchars($c['login_badge_text'] ?? 'Central de Atendimento & Suporte'); ?>
                                    </div>
                                    <h4 id="bks-preview-login-heading" style="margin: 12px 0 2px 0; font-size: 1.25rem; font-weight: 800; color: #0f172a;"><?php echo htmlspecialchars($c['login_title'] ?? 'Entrar no Sistema'); ?></h4>
                                    <span id="bks-preview-login-subtitle" style="font-size: 11px; color: #64748b;"><?php echo htmlspecialchars($c['login_subtitle'] ?? 'Acesso corporativo seguro'); ?></span>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div>
                                        <label style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 3px;">Usuário</label>
                                        <input type="text" placeholder="nome.sobrenome" readonly style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 12px; background: #f8fafc;">
                                    </div>
                                    <div>
                                        <label style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 3px;">Senha</label>
                                        <input type="password" value="secretpassword" readonly style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 12px; background: #f8fafc;">
                                    </div>
                                    <button type="button" id="bks-preview-submit-btn" style="width: 100%; color: #ffffff; padding: 10px; border: none; border-radius: 9px; font-weight: 700; font-size: 12px; margin-top: 4px; cursor: pointer;">
                                        <?php echo htmlspecialchars($c['login_btn_text'] ?? 'Acessar Atendimento'); ?>
                                    </button>
                                </div>

                                <div id="bks-preview-register-box" style="margin-top: 1rem; text-align: center; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 10px;">
                                    <span id="bks-preview-register-text" style="display: block; font-size: 11px; font-weight: 700; color: #003876;">
                                        ➕ <?php echo htmlspecialchars($c['external_register_text'] ?? 'Auto-Cadastro de Usuários Externos'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 2: PAINEL INTERNO DO GLPI COM MENU RECOLHÍVEL INTERATIVO -->
                <div id="bks-preview-tab-internal" class="brandkit-tab-pane" style="display: none; background: #f8fafc; min-height: 530px;">

                    <!-- Sub-barra interativa para testar Menu Inteiro vs Menu Recolhido -->
                    <div class="brandkit-sim-subbar">
                        <span>Simulador Interativo da Sidebar do GLPI 11:</span>
                        <button type="button" id="bks-toggle-fold-btn" class="brandkit-fold-toggle-btn">
                            <span>📐 Testar Menu Recolhido (70px)</span>
                        </button>
                    </div>

                    <!-- Layout fiel ao GLPI 11 Vertical: Sidebar à esquerda + Coluna direita com Header e Conteúdo -->
                    <div style="display: flex; min-height: 495px;">

                        <!-- Sidebar Vertical do GLPI -->
                        <div id="bks-preview-internal-sidebar" style="
                            width: 225px;
                            background: #0A1428;
                            border-right: 1px solid rgba(255,255,255,0.08);
                            padding: 0.85rem 0.65rem;
                            display: flex;
                            flex-direction: column;
                            gap: 4px;
                            flex-shrink: 0;
                        ">
                            <!-- Área navbar-brand com troca automática entre Logo Inteiro e Ícone Recolhido -->
                            <div style="padding: 0.35rem 0.25rem 0.85rem 0.25rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 0.4rem; display: flex; align-items: center; justify-content: center; min-height: 52px;">
                                <img id="bks-preview-sidebar-logo" src="<?php echo htmlspecialchars($effectiveSidebarLogo); ?>" alt="Logo Expandido" style="max-width: 145px; max-height: 40px; object-fit: contain; transition: all 0.2s ease;">
                                <img id="bks-preview-sidebar-logo-collapsed" src="<?php echo htmlspecialchars($effectiveCollapsedLogo); ?>" alt="Ícone Recolhido" style="display: none; width: 38px; height: 38px; object-fit: contain; transition: all 0.2s ease;">
                            </div>

                            <div class="bks-sidebar-item" title="Encontrar um menu" style="padding: 6px 10px; border-radius: 8px; font-size: 10.5px; font-weight: 500; display: flex; align-items: center; gap: 8px; color: #94a3b8; opacity: 0.85;">
                                <span>🔍</span>
                                <span class="bks-sim-label">Encontrar um menu</span>
                            </div>

                            <div class="bks-sidebar-item-active" id="bks-preview-sidebar-active-item" title="Assistência / Chamados" style="
                                background: rgba(255,255,255,0.08);
                                color: #38bdf8;
                                border-left: 3px solid #38bdf8;
                                padding: 7px 10px;
                                border-radius: 10px;
                                font-size: 11px;
                                font-weight: 700;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <span>🎧</span>
                                <span class="bks-sim-label">Assistência</span>
                            </div>

                            <div class="bks-sidebar-item" title="Ativos" style="padding: 7px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>💻</span>
                                <span class="bks-sim-label">Ativos</span>
                            </div>

                            <div class="bks-sidebar-item" title="Administração" style="padding: 7px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>👥</span>
                                <span class="bks-sim-label">Administração</span>
                            </div>

                            <div class="bks-sidebar-item" title="Configurar > BrandKit Studio" style="padding: 7px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>🎨</span>
                                <span class="bks-sim-label">BrandKit Studio</span>
                            </div>

                            <!-- Botão autêntico "« Recolher menu" no rodapé da sidebar do GLPI -->
                            <button type="button" id="bks-sim-reduce-btn" title="Clique para recolher ou expandir o menu" style="
                                margin-top: auto;
                                background: rgba(255,255,255,0.05);
                                border: 1px solid rgba(255,255,255,0.1);
                                color: #cbd5e1;
                                padding: 7px 10px;
                                border-radius: 8px;
                                font-size: 10.5px;
                                font-weight: 600;
                                cursor: pointer;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 6px;
                            ">
                                <span id="bks-sim-reduce-arrow">«</span>
                                <span class="bks-sim-reduce-label">Recolher menu</span>
                            </button>
                        </div>

                        <!-- Coluna Direita Simulada (Navbar Superior + Corpo da Página) -->
                        <div style="flex: 1; display: flex; flex-direction: column; min-width: 0;">
                            <div id="bks-preview-internal-navbar" style="
                                background: #0A1428;
                                border-bottom: 3px solid #C0122D;
                                padding: 0.6rem 1.1rem;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                color: #ffffff;
                                gap: 8px;
                            ">
                                <div style="font-size: 10.5px; font-weight: 600; opacity: 0.9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    🏠 Home / Configurar / <strong>BrandKit Studio</strong>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                    <span style="background: rgba(255,255,255,0.12); padding: 3px 9px; border-radius: 6px; font-size: 10px;">🔍 Pesquisar...</span>
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: #020617;">GL</div>
                                </div>
                            </div>

                            <div id="bks-preview-internal-body" style="flex: 1; padding: 1.25rem; background: #f8fafc; transition: all 0.25s ease;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <div>
                                        <h5 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #0f172a;">Visão Geral do Suporte</h5>
                                        <span style="font-size: 10.5px; color: #64748b;">Central de Serviços de TI</span>
                                    </div>
                                    <button type="button" id="bks-preview-internal-btn" style="background: #003876; color: #ffffff; border: none; padding: 6px 12px; border-radius: 8px; font-size: 10.5px; font-weight: 700; cursor: pointer;">
                                        + Novo Chamado
                                    </button>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 1rem;">
                                    <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px;">
                                        <span style="font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Novos</span>
                                        <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;">14</div>
                                    </div>
                                    <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px;">
                                        <span style="font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Atribuídos</span>
                                        <div style="font-size: 1.2rem; font-weight: 800; color: #0284c7;">8</div>
                                    </div>
                                    <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px;">
                                        <span style="font-size: 9.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">SLA Cumprido</span>
                                        <div style="font-size: 1.2rem; font-weight: 800; color: #10b981;">98.4%</div>
                                    </div>
                                </div>

                                <div class="bks-preview-subcard" id="bks-preview-main-panel" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                                    <span style="font-size: 11px; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">Chamados Recentes</span>
                                    <div style="display: flex; flex-direction: column; gap: 6px;">
                                        <div style="display: flex; justify-content: space-between; font-size: 10px; padding: 4px 0; border-bottom: 1px solid #f1f5f9;">
                                            <span style="font-weight: 600; color: #1e293b;">#1042 - Acesso ao Sistema e VPN Corporativa</span>
                                            <span style="color: #003876; font-weight: 700;">Em atendimento</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 10px; padding: 4px 0;">
                                            <span style="font-weight: 600; color: #1e293b;">#1041 - Configuração de Estação de Trabalho</span>
                                            <span style="color: #10b981; font-weight: 700;">Solucionado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA 3: NAVEGADOR & FAVICON -->
                <div id="bks-preview-tab-browser" class="brandkit-tab-pane" style="display: none; background: #0f172a; min-height: 530px; padding: 1.5rem;">
                    <div style="background: #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155;">
                        <div style="background: #0f172a; padding: 8px 12px 0 12px; display: flex; align-items: center; gap: 8px;">
                            <div style="display: flex; gap: 6px; margin-right: 8px;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                            </div>
                            <div style="background: #1e293b; color: #ffffff; padding: 8px 14px; border-radius: 8px 8px 0 0; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; max-width: 260px;">
                                <img id="bks-preview-favicon-tab" src="<?php echo htmlspecialchars($c['favicon_url']); ?>" alt="Favicon" style="width: 16px; height: 16px; object-fit: contain; border-radius: 2px;">
                                <span id="bks-preview-tab-title-text" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">GLPI • Central de Atendimento</span>
                                <span style="font-size: 10px; opacity: 0.6; margin-left: auto;">✕</span>
                            </div>
                        </div>

                        <div style="background: #1e293b; padding: 8px 12px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #334155;">
                            <span style="color: #64748b; font-size: 12px;">← → ⟳</span>
                            <div style="flex: 1; background: #0f172a; padding: 6px 12px; border-radius: 8px; font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 8px; border: 1px solid #334155;">
                                <span style="color: #10b981; font-weight: 800;">🔒 https://</span>
                                <span style="color: #f8fafc; font-weight: 600;">suporte.suaempresa.com.br</span>
                            </div>
                        </div>

                        <div style="padding: 2.25rem; text-align: center; background: #1e293b;">
                            <h4 style="color: #f8fafc; margin: 0 0 0.5rem 0; font-size: 1.05rem; font-weight: 800;">🌐 Escalas de Renderização do Favicon</h4>
                            <p style="color: #94a3b8; font-size: 0.8rem; margin: 0 0 1.75rem 0;">Visualize a nitidez em abas, favoritos e atalhos mobile.</p>
                            <div style="display: flex; justify-content: center; align-items: flex-end; gap: 1.75rem; flex-wrap: wrap;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="background: #0f172a; padding: 12px; border-radius: 10px; border: 1px solid #334155;"><img id="bks-preview-fav-16" src="<?php echo htmlspecialchars($c['favicon_url']); ?>" style="width:16px;height:16px;object-fit:contain;"></div>
                                    <span style="color:#94a3b8;font-size:10px;font-weight:700;">16×16</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="background: #0f172a; padding: 12px; border-radius: 10px; border: 1px solid #334155;"><img id="bks-preview-fav-32" src="<?php echo htmlspecialchars($c['favicon_url']); ?>" style="width:32px;height:32px;object-fit:contain;"></div>
                                    <span style="color:#94a3b8;font-size:10px;font-weight:700;">32×32</span>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="background: #0f172a; padding: 14px; border-radius: 12px; border: 1px solid #334155;"><img id="bks-preview-fav-64" src="<?php echo htmlspecialchars($c['favicon_url']); ?>" style="width:64px;height:64px;object-fit:contain;"></div>
                                    <span style="color:#94a3b8;font-size:10px;font-weight:700;">64×64</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
    Html::footer();
} catch (\Throwable $e) {
    if (class_exists('Toolbox')) {
        Toolbox::logInFile('php-errors', "BrandKitSlater Form Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    }
    echo '<div style="margin:2rem;padding:1.5rem;background:#fef2f2;border:2px solid #ef4444;border-radius:12px;color:#991b1b;">';
    echo '<h3>Erro ao renderizar painel BrandKitSlater</h3><p>' . htmlspecialchars($e->getMessage()) . '</p></div>';
}
