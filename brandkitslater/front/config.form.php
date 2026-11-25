<?php
/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Painel de Administração e Studio com Live Preview
 * -----------------------------------------------------------------------------
 */

try {
    // Compatibilidade: GLPI 10 usa includes.php; GLPI 11 já inicializa Session no front controller
    if (!class_exists('Session') && file_exists(__DIR__ . '/../../../inc/includes.php')) {
        include_once __DIR__ . '/../../../inc/includes.php';
    }

    // Restrição de Acesso: Apenas administradores com permissão de configuração
    if (class_exists('Session')) {
        Session::checkRight("config", UPDATE);
    }

    require_once __DIR__ . '/../inc/config.class.php';
    require_once __DIR__ . '/../inc/cssgenerator.class.php';

    // Processa salvamento do formulário
    if (isset($_POST['save_brandkit'])) {
        PluginBrandkitslaterConfig::saveConfig($_POST, $_FILES);
        Html::redirect('/plugins/brandkitslater/front/config.form.php?saved=1');
    }

    $c = PluginBrandkitslaterConfig::getConfig();

    Html::header("BrandKitSlater", '/plugins/brandkitslater/front/config.form.php', "config", "plugins");

    $pluginWebDir = class_exists('Plugin') && method_exists('Plugin', 'getWebDir') ? Plugin::getWebDir('brandkitslater') : '/plugins/brandkitslater';
    echo '<link rel="stylesheet" type="text/css" href="' . $pluginWebDir . '/css/brandkit_admin.css">';
    echo '<script src="' . $pluginWebDir . '/js/brandkit_preview.js"></script>';

    if (isset($_GET['saved'])) {
        echo '<div style="margin-bottom: 1.5rem; background: #ecfdf5; border: 1.5px solid #10b981; color: #065f46; padding: 1rem 1.5rem; border-radius: 12px; font-weight: 700; display: flex; align-items: center; gap: 10px;">';
        echo '<span>✅ Configurações do BrandKitSlater aplicadas com sucesso e arquivos compilados!</span>';
        echo '</div>';
    }
?>

<div class="brandkit-studio-container" style="padding: 1.5rem 2rem;">

    <!-- Cabeçalho do BrandKitSlater -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; background: #0f172a; padding: 1.5rem 2rem; border-radius: 16px; color: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-left: 6px solid #3b82f6; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em;">BrandKitSlater Studio</span>
                <span style="font-size: 11px; background: rgba(59, 130, 246, 0.25); color: #60a5fa; padding: 2px 8px; border-radius: 6px; font-weight: 700; border: 1px solid rgba(96, 165, 250, 0.4);">v1.0 Community Lite</span>
            </div>
            <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #94a3b8;">Estúdio Open-Source de Identidade Visual, Layouts de Login e Temas para GLPI 10 e 11</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button type="button" onclick="loadBrandkitColorPreset('slate')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">🏢 Slate</button>
            <button type="button" onclick="loadBrandkitColorPreset('clean')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">☀️ Clean</button>
            <button type="button" onclick="loadBrandkitColorPreset('emerald')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">🌿 Emerald</button>
            <button type="button" onclick="loadBrandkitColorPreset('bordeaux')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">🍷 Bordeaux</button>
            <button type="button" onclick="loadBrandkitColorPreset('midnight')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">🌌 Midnight</button>
            <button type="button" onclick="loadBrandkitColorPreset('classic')" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.25); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">🖥️ Classic</button>
        </div>
    </div>

    <!-- Área de Trabalho: Lado a Lado (Controles vs Live Preview) -->
    <div class="brandkit-studio-wrapper">

        <!-- Coluna de Controles (Esquerda) -->
        <div class="brandkit-controls-col">
            <form method="post" action="/plugins/brandkitslater/front/config.form.php" enctype="multipart/form-data" id="brandkit-config-form">
                <input type="hidden" name="save_brandkit" value="1">
                <input type="hidden" name="preset" id="bks_preset_input" value="<?php echo htmlspecialchars($c['preset']); ?>">
                <?php
                if (class_exists('Session') && method_exists('Session', 'getNewCSRFToken')) {
                    echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';
                }
                ?>

                <!-- Bloco 1: Tela de Login -->
                <div class="brandkit-card">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                        <span>🚪 Layout e Posição da Tela de Login</span>
                    </h3>

                    <!-- Seletor de Posição dos Campos -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Posicionamento dos Campos</label>
                        <div class="brandkit-position-grid">
                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'left' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_left" value="left" style="display:none;" <?php echo ($c['login_position'] === 'left' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: flex-start;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">Canto Esquerdo</span>
                            </label>

                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'center' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_center" value="center" style="display:none;" <?php echo ($c['login_position'] === 'center' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: center;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">Centro (Clássico)</span>
                            </label>

                            <label class="brandkit-position-card <?php echo ($c['login_position'] === 'right' ? 'active' : ''); ?>">
                                <input type="radio" name="login_position" id="bks_pos_right" value="right" style="display:none;" <?php echo ($c['login_position'] === 'right' ? 'checked' : ''); ?>>
                                <div class="brandkit-pos-icon" style="justify-content: flex-end;"><div class="brandkit-pos-box"></div></div>
                                <span style="font-weight: 700; font-size: 0.85rem; color: #1e293b;">Canto Direito ⭐</span>
                            </label>
                        </div>
                    </div>

                    <!-- Estilo do Card -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Estilo Visual do Card</label>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_glass" value="glassmorphism" <?php echo ($c['card_style'] === 'glassmorphism' ? 'checked' : ''); ?>> 💎 Glassmorphism (Vidro Fosco)
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_flat" value="flat" <?php echo ($c['card_style'] === 'flat' ? 'checked' : ''); ?>> 🧊 Clean Sólido
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="card_style" id="bks_style_bordered" value="bordered" <?php echo ($c['card_style'] === 'bordered' ? 'checked' : ''); ?>> 📦 Borda Institucional
                            </label>
                        </div>
                    </div>

                    <!-- Sliders de Dimensão do Card -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Largura do Card de Login</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_width" id="bks_card_width" min="360" max="620" value="<?php echo htmlspecialchars($c['card_width']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_width" data-unit="px"><?php echo htmlspecialchars($c['card_width']); ?>px</span>
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Arredondamento das Bordas</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_radius" id="bks_card_radius" min="0" max="32" value="<?php echo htmlspecialchars($c['card_radius']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_radius" data-unit="px"><?php echo htmlspecialchars($c['card_radius']); ?>px</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sliders de Vidro (Blur e Opacidade) -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Desfoque do Vidro (Blur)</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_blur" id="bks_card_blur" min="0" max="40" value="<?php echo htmlspecialchars($c['card_blur']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_blur" data-unit="px"><?php echo htmlspecialchars($c['card_blur']); ?>px</span>
                            </div>
                        </div>

                        <div>
                            <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Opacidade do Card</label>
                            <div class="brandkit-slider-group">
                                <input type="range" name="card_opacity" id="bks_card_opacity" min="50" max="100" value="<?php echo htmlspecialchars($c['card_opacity']); ?>">
                                <span class="brandkit-slider-value" data-target="bks_card_opacity" data-unit="%"><?php echo htmlspecialchars($c['card_opacity']); ?>%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Imagem de Fundo & Upload -->
                    <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                            🖼️ Imagem de Fundo da Tela de Login
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; background: #003876; color: #fff; padding: 0.65rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                                <span>📤 Enviar Nova Imagem (JPG, PNG, WebP)</span>
                                <input type="file" name="bg_image_file" id="bks_bg_image_file" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('bg_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_bg_image_url').value = e.target.result; document.getElementById('bks_bg_image_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                            </label>
                            <span id="bg_file_name" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Substitui a imagem atual no servidor</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" name="bg_image_url" id="bks_bg_image_url" value="<?php echo htmlspecialchars($c['bg_image_url']); ?>" placeholder="/plugins/brandkitslater/pics/login_bg.jpg" style="flex: 1; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                            <button type="button" onclick="document.getElementById('bks_bg_image_url').value='/plugins/brandkitslater/pics/login_bg.jpg'; document.getElementById('bks_bg_image_url').dispatchEvent(new Event('input'));" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer;">🌌 Wallpaper Padrão</button>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Intensidade do Gradiente de Fundo (Contraste)</label>
                        <div class="brandkit-slider-group">
                            <input type="range" name="bg_overlay_opacity" id="bks_bg_overlay_opacity" min="0" max="95" value="<?php echo htmlspecialchars($c['bg_overlay_opacity']); ?>">
                            <span class="brandkit-slider-value" data-target="bks_bg_overlay_opacity" data-unit="%"><?php echo htmlspecialchars($c['bg_overlay_opacity']); ?>%</span>
                        </div>
                    </div>

                    <!-- Auto-Cadastro Externo -->
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span style="font-weight: 700; font-size: 0.9rem; color: #0f172a; display: block;">Botão de Auto-Cadastro Externo</span>
                            <span style="font-size: 0.8rem; color: #64748b;">Exibe atalho direto para o portal `/cadastro/` no login</span>
                        </div>
                        <input type="checkbox" name="enable_external_register" id="bks_enable_external_register" value="1" <?php echo (!empty($c['enable_external_register']) ? 'checked' : ''); ?> style="width: 20px; height: 20px; accent-color: #003876;">
                    </div>
                </div>

                <!-- Bloco 2: 🎨 Cores e Estilização do Ambiente -->
                <div class="brandkit-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 10px;">
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>🎨 Cores do Ambiente e Painel Interno</span>
                        </h3>
                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.82rem; font-weight: 700; color: #334155; background: #f8fafc; padding: 6px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1;">
                            <input type="checkbox" name="enable_internal_theme" id="bks_enable_internal_theme" value="1" <?php echo (!empty($c['enable_internal_theme']) ? 'checked' : ''); ?> style="width: 16px; height: 16px; accent-color: #003876;">
                            <span>Aplicar cores no painel interno do GLPI</span>
                        </label>
                    </div>
                    <p style="font-size: 0.8rem; color: #64748b; margin-top: 0; margin-bottom: 1.25rem;">
                        🛡️ <strong>Modo Híbrido:</strong> Quando desmarcado, mantém o painel interno no <strong>Light Mode padrão do GLPI</strong> e aplica as cores apenas na tela de login. Ao marcar, personaliza cabeçalho, barra lateral, botões e cartões!
                    </p>

                    <!-- Paletas Rápidas de 1 Clique -->
                    <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Paletas Prontas de 1 Clique</label>
                    <div class="brandkit-color-presets">
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('slate')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#1e293b;"></span>
                                <span class="brandkit-preset-dot" style="background:#6366f1;"></span>
                                <span class="brandkit-preset-dot" style="background:#0f172a;"></span>
                            </span>
                            <span>🏢 Corporate Slate</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('clean')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#2563eb;"></span>
                                <span class="brandkit-preset-dot" style="background:#f59e0b;"></span>
                                <span class="brandkit-preset-dot" style="background:#ffffff;"></span>
                            </span>
                            <span>☀️ Clean Light</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('emerald')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#059669;"></span>
                                <span class="brandkit-preset-dot" style="background:#f59e0b;"></span>
                                <span class="brandkit-preset-dot" style="background:#064e3b;"></span>
                            </span>
                            <span>🌿 Emerald Nature</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('bordeaux')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#881337;"></span>
                                <span class="brandkit-preset-dot" style="background:#d97706;"></span>
                                <span class="brandkit-preset-dot" style="background:#4c0519;"></span>
                            </span>
                            <span>🍷 Bordeaux</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('midnight')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#0284c7;"></span>
                                <span class="brandkit-preset-dot" style="background:#38bdf8;"></span>
                                <span class="brandkit-preset-dot" style="background:#020617;"></span>
                            </span>
                            <span>🌌 Midnight Cyber</span>
                        </button>
                        <button type="button" class="brandkit-preset-pill" onclick="loadBrandkitColorPreset('classic')">
                            <span class="brandkit-preset-dots">
                                <span class="brandkit-preset-dot" style="background:#204a87;"></span>
                                <span class="brandkit-preset-dot" style="background:#f57900;"></span>
                                <span class="brandkit-preset-dot" style="background:#2e3436;"></span>
                            </span>
                            <span>🦊 GLPI Classic</span>
                        </button>
                    </div>

                    <!-- Seção A: Cores Principais -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">🎯 Cores de Destaque e Ações</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Cor Primária (Botões/Links)</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="primary_color" id="bks_primary_color" value="<?php echo htmlspecialchars($c['primary_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_primary_color_text" value="<?php echo htmlspecialchars($c['primary_color']); ?>" oninput="document.getElementById('bks_primary_color').value=this.value; document.getElementById('bks_primary_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Hover do Botão Primário</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="primary_hover" id="bks_primary_hover" value="<?php echo htmlspecialchars($c['primary_hover']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_primary_hover_text" value="<?php echo htmlspecialchars($c['primary_hover']); ?>" oninput="document.getElementById('bks_primary_hover').value=this.value; document.getElementById('bks_primary_hover').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Cor de Destaque / Accent</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="accent_color" id="bks_accent_color" value="<?php echo htmlspecialchars($c['accent_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_accent_color_text" value="<?php echo htmlspecialchars($c['accent_color']); ?>" oninput="document.getElementById('bks_accent_color').value=this.value; document.getElementById('bks_accent_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção B: Barra Superior (Navbar Header) -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">🔝 Barra Superior (Header Navbar)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Fundo da Navbar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_bg" id="bks_navbar_bg" value="<?php echo htmlspecialchars($c['navbar_bg']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_bg_text" value="<?php echo htmlspecialchars($c['navbar_bg']); ?>" oninput="document.getElementById('bks_navbar_bg').value=this.value; document.getElementById('bks_navbar_bg').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Texto e Ícones da Navbar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_text_color" id="bks_navbar_text_color" value="<?php echo htmlspecialchars($c['navbar_text_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_text_color_text" value="<?php echo htmlspecialchars($c['navbar_text_color']); ?>" oninput="document.getElementById('bks_navbar_text_color').value=this.value; document.getElementById('bks_navbar_text_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Borda Inferior da Navbar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="navbar_border_color" id="bks_navbar_border_color" value="<?php echo htmlspecialchars($c['navbar_border_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_navbar_border_color_text" value="<?php echo htmlspecialchars($c['navbar_border_color']); ?>" oninput="document.getElementById('bks_navbar_border_color').value=this.value; document.getElementById('bks_navbar_border_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção C: Barra Lateral (Sidebar Vertical) -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">🧭 Barra Lateral (Sidebar Vertical)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Fundo da Sidebar</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_bg" id="bks_sidebar_bg" value="<?php echo htmlspecialchars($c['sidebar_bg']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_bg_text" value="<?php echo htmlspecialchars($c['sidebar_bg']); ?>" oninput="document.getElementById('bks_sidebar_bg').value=this.value; document.getElementById('bks_sidebar_bg').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Texto dos Menus</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_text_color" id="bks_sidebar_text_color" value="<?php echo htmlspecialchars($c['sidebar_text_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_text_color_text" value="<?php echo htmlspecialchars($c['sidebar_text_color']); ?>" oninput="document.getElementById('bks_sidebar_text_color').value=this.value; document.getElementById('bks_sidebar_text_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Cor do Item Ativo</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="sidebar_active_color" id="bks_sidebar_active_color" value="<?php echo htmlspecialchars($c['sidebar_active_color']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_sidebar_active_color_text" value="<?php echo htmlspecialchars($c['sidebar_active_color']); ?>" oninput="document.getElementById('bks_sidebar_active_color').value=this.value; document.getElementById('bks_sidebar_active_color').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Fundo do Item Ativo</label>
                                <input type="text" name="sidebar_active_bg" id="bks_sidebar_active_bg" value="<?php echo htmlspecialchars($c['sidebar_active_bg']); ?>" placeholder="rgba(255,255,255,0.08)" style="width:100%; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Seção D: Superfícies e Cards -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">📄 Superfícies e Fundo Geral</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Fundo da Página (Body)</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="body_bg" id="bks_body_bg" value="<?php echo htmlspecialchars($c['body_bg']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_body_bg_text" value="<?php echo htmlspecialchars($c['body_bg']); ?>" oninput="document.getElementById('bks_body_bg').value=this.value; document.getElementById('bks_body_bg').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.35rem;">Fundo dos Cartões e Painéis</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="color" name="card_bg" id="bks_card_bg" value="<?php echo htmlspecialchars($c['card_bg']); ?>" style="width: 42px; height: 38px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" id="bks_card_bg_text" value="<?php echo htmlspecialchars($c['card_bg']); ?>" oninput="document.getElementById('bks_card_bg').value=this.value; document.getElementById('bks_card_bg').dispatchEvent(new Event('input'));" style="flex:1; padding: 0.45rem 0.6rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bloco 3: 📐 Ícones, Logotipo Institucional e Formatos Visuais -->
                <div class="brandkit-card">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                        <span>📐 Personalização de Logotipos, Ícones e Formatos</span>
                    </h3>

                    <!-- Logo Institucional -->
                    <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                            🏷️ Logotipo Institucional (PNG, SVG, JPG)
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; background: #003876; color: #fff; padding: 0.65rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                                <span>📤 Enviar Novo Logo</span>
                                <input type="file" name="logo_file" id="bks_logo_file" accept="image/png,image/svg+xml,image/jpeg,image/webp" style="display:none;" onchange="if(this.files[0]) { document.getElementById('logo_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_logo_url').value = e.target.result; document.getElementById('bks_logo_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                            </label>
                            <span id="logo_file_name" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Substitui o logotipo atual</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" name="logo_url" id="bks_logo_url" placeholder="/plugins/brandkitslater/pics/logo.png" value="<?php echo htmlspecialchars($c['logo_url']); ?>" style="flex: 1; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                            <button type="button" onclick="document.getElementById('bks_logo_url').value='/plugins/brandkitslater/pics/logo.png'; document.getElementById('bks_logo_url').dispatchEvent(new Event('input'));" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer;">🛡️ Logo Padrão</button>
                        </div>
                    </div>

                    <!-- Favicon da Aba -->
                    <div style="margin-bottom: 1.5rem; background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 0.5rem;">
                            🌐 Favicon da Aba do Navegador (.ico / .png / .svg)
                        </label>
                        <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
                            <label style="display: inline-flex; align-items: center; gap: 8px; background: #003876; color: #fff; padding: 0.65rem 1.2rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                                <span>📤 Enviar Favicon</span>
                                <input type="file" name="favicon_file" id="bks_favicon_file" accept=".ico,image/png,image/svg+xml" style="display:none;" onchange="if(this.files[0]) { document.getElementById('favicon_file_name').innerText = '📁 ' + this.files[0].name; const reader = new FileReader(); reader.onload = function(e){ document.getElementById('bks_favicon_url').value = e.target.result; document.getElementById('bks_favicon_url').dispatchEvent(new Event('input')); }; reader.readAsDataURL(this.files[0]); }">
                            </label>
                            <span id="favicon_file_name" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Substitui o ícone da aba no GLPI</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" name="favicon_url" id="bks_favicon_url" placeholder="/plugins/brandkitslater/pics/favicon.ico" value="<?php echo htmlspecialchars($c['favicon_url']); ?>" style="flex: 1; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem;">
                            <button type="button" onclick="document.getElementById('bks_favicon_url').value='/plugins/brandkitslater/pics/favicon.ico'; document.getElementById('bks_favicon_url').dispatchEvent(new Event('input'));" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 0.6rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8rem; cursor: pointer;">🌐 Favicon Padrão</button>
                        </div>
                    </div>

                    <!-- Seletor de Formato do Logo (Shapes) -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.5rem;">Formato Visual do Logotipo</label>
                        <div class="brandkit-shape-grid">
                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'standard' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_standard" value="standard" style="display:none;" <?php echo ($c['logo_shape'] === 'standard' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo standard">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Padrão</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Sem cortes</span>
                            </label>

                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'rounded' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_rounded" value="rounded" style="display:none;" <?php echo ($c['logo_shape'] === 'rounded' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo rounded">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Arredondado</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Raio de 12px</span>
                            </label>

                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'circle' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_circle" value="circle" style="display:none;" <?php echo ($c['logo_shape'] === 'circle' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo circle">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Circular</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Corte 50%</span>
                            </label>

                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'pill' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_pill" value="pill" style="display:none;" <?php echo ($c['logo_shape'] === 'pill' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo pill">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Cápsula</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Bordas totais</span>
                            </label>

                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'bordered' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_bordered" value="bordered" style="display:none;" <?php echo ($c['logo_shape'] === 'bordered' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo bordered">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Moldura</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Borda institucional</span>
                            </label>

                            <label class="brandkit-shape-card <?php echo ($c['logo_shape'] === 'glow' ? 'active' : ''); ?>">
                                <input type="radio" name="logo_shape" id="bks_shape_glow" value="glow" style="display:none;" <?php echo ($c['logo_shape'] === 'glow' ? 'checked' : ''); ?>>
                                <div class="brandkit-shape-icon-demo glow">LOGO</div>
                                <span style="font-weight: 700; font-size: 0.8rem; color: #1e293b;">Glow Neon</span>
                                <span style="font-size: 0.7rem; color: #64748b;">Iluminado</span>
                            </label>
                        </div>
                    </div>

                    <!-- Proporção e Encaixe (Object-Fit) -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Ajuste de Proporção (Object Fit)</label>
                        <select name="logo_fit" id="bks_logo_fit" style="width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600;">
                            <option value="contain" <?php echo ($c['logo_fit'] === 'contain' ? 'selected' : ''); ?>>Conter Proporcional (Recomendado - sem distorção)</option>
                            <option value="cover" <?php echo ($c['logo_fit'] === 'cover' ? 'selected' : ''); ?>>Preencher Total (Pode recortar bordas)</option>
                            <option value="scale-down" <?php echo ($c['logo_fit'] === 'scale-down' ? 'selected' : ''); ?>>Reduzir se Maior (Preserva tamanho original se pequeno)</option>
                        </select>
                    </div>

                    <!-- Sliders de Dimensão do Logo na Barra Lateral (Sidebar) -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0; margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">🧭 Logo da Barra Lateral (Painel Interno)</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Largura Máxima da Sidebar</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="sidebar_logo_width" id="bks_sidebar_logo_width" min="50" max="260" value="<?php echo htmlspecialchars($c['sidebar_logo_width']); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_sidebar_logo_width" data-unit="px"><?php echo htmlspecialchars($c['sidebar_logo_width']); ?>px</span>
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Altura Máxima da Sidebar</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="sidebar_logo_height" id="bks_sidebar_logo_height" min="20" max="70" value="<?php echo htmlspecialchars($c['sidebar_logo_height']); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_sidebar_logo_height" data-unit="px"><?php echo htmlspecialchars($c['sidebar_logo_height']); ?>px</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sliders de Dimensão do Logo na Tela de Login -->
                    <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 0.9rem; font-weight: 700; color: #1e293b;">🚪 Logo do Cartão de Login</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Largura Máxima do Login</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="login_logo_width" id="bks_login_logo_width" min="80" max="360" value="<?php echo htmlspecialchars($c['login_logo_width']); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_login_logo_width" data-unit="px"><?php echo htmlspecialchars($c['login_logo_width']); ?>px</span>
                                </div>
                            </div>

                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Altura Máxima do Login</label>
                                <div class="brandkit-slider-group">
                                    <input type="range" name="login_logo_height" id="bks_login_logo_height" min="25" max="120" value="<?php echo htmlspecialchars($c['login_logo_height']); ?>">
                                    <span class="brandkit-slider-value" data-target="bks_login_logo_height" data-unit="px"><?php echo htmlspecialchars($c['login_logo_height']); ?>px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bloco 4: Tipografia, Rodapé & CSS Customizado -->
                <div class="brandkit-card">
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem;">
                        🔤 Tipografia, Rodapé & CSS Customizado
                    </h3>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Fonte do Sistema</label>
                        <select name="font_family" id="bks_font_family" style="width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-weight: 600;">
                            <option value="Plus Jakarta Sans" <?php echo ($c['font_family'] === 'Plus Jakarta Sans' ? 'selected' : ''); ?>>Plus Jakarta Sans (Recomendada Enterprise)</option>
                            <option value="Inter" <?php echo ($c['font_family'] === 'Inter' ? 'selected' : ''); ?>>Inter (Padrão SaaS Moderno)</option>
                            <option value="Montserrat" <?php echo ($c['font_family'] === 'Montserrat' ? 'selected' : ''); ?>>Montserrat (Imponente & Jurídico)</option>
                            <option value="Roboto" <?php echo ($c['font_family'] === 'Roboto' ? 'selected' : ''); ?>>Roboto (Técnica & Neutra)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Texto do Rodapé da Tela de Login</label>
                        <input type="text" name="footer_text" id="bks_footer_text" value="<?php echo htmlspecialchars($c['footer_text']); ?>" style="width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px;">
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 0.4rem;">Regras de CSS Adicionais (Avançado)</label>
                        <textarea name="custom_css" id="bks_custom_css" rows="3" placeholder="/* CSS customizado para ajustes finos adicionais */" style="width: 100%; padding: 0.6rem 0.8rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars($c['custom_css']); ?></textarea>
                    </div>
                </div>

                <!-- Botão de Salvar Fixo Flutuante -->
                <div class="brandkit-save-bar">
                    <div style="font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                        <span>As alterações entram em vigor imediatamente para todos os usuários.</span>
                    </div>
                    <button type="submit" class="brandkit-save-btn">
                        <span>💾 Salvar e Aplicar Tema</span>
                    </button>
                </div>

            </form>
        </div>

        <!-- Coluna de Live Preview (Direita) -->
        <div class="brandkit-preview-col">
            <div class="brandkit-preview-frame" id="bks-preview-frame">

                <!-- Barra de Ferramentas Superior do Preview -->
                <div class="brandkit-preview-toolbar">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: flex; gap: 6px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; opacity: 0.85;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; opacity: 0.85;"></span>
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; opacity: 0.85;"></span>
                        </div>
                        <span style="font-size: 0.78rem; font-weight: 700; letter-spacing: 0.04em; color: #e2e8f0;">STUDIO PREVIEW</span>
                        <span id="bks-contrast-badge" class="brandkit-contrast-badge">✨ WCAG AAA (8.4:1)</span>
                    </div>
                    <div class="brandkit-device-segmented">
                        <button type="button" class="brandkit-device-btn active" data-device="desktop">🖥️ Desktop</button>
                        <button type="button" class="brandkit-device-btn" data-device="tablet">📟 Tablet</button>
                        <button type="button" class="brandkit-device-btn" data-device="mobile">📱 Celular</button>
                    </div>
                </div>

                <!-- Abas do Preview (Login / Painel Interno / Aba do Navegador) -->
                <div class="brandkit-preview-tabs">
                    <button type="button" class="brandkit-tab-btn active" data-tab="login">🚪 Tela de Login</button>
                    <button type="button" class="brandkit-tab-btn" data-tab="internal">🧭 Painel Interno (Sidebar & Navbar)</button>
                    <button type="button" class="brandkit-tab-btn" data-tab="browser">🌐 Aba do Navegador (Favicon)</button>
                </div>

                <!-- ============================================================ -->
                <!-- ABA 1: PREVIEW DA TELA DE LOGIN                               -->
                <!-- ============================================================ -->
                <div id="bks-preview-tab-login" class="brandkit-tab-pane" style="display: block;">
                    <div id="bks-preview-container" style="
                        position: relative;
                        min-height: 520px;
                        display: flex;
                        align-items: center;
                        justify-content: flex-end;
                        background-size: cover;
                        background-position: left center;
                        background-repeat: no-repeat;
                        padding: 2rem 5vw;
                        overflow: hidden;
                        transition: all 0.3s ease;
                    ">
                        <!-- Overlay de Gradiente -->
                        <div id="bks-preview-overlay" style="position: absolute; inset: 0; pointer-events: none; z-index: 1;"></div>

                        <!-- Card no Preview -->
                        <div id="bks-preview-card" style="
                            position: relative;
                            z-index: 2;
                            width: 100%;
                            max-width: 440px;
                            border-radius: 22px;
                            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                            overflow: hidden;
                            transition: all 0.25s ease;
                        ">
                            <!-- Barra de Acento Superior -->
                            <div id="bks-preview-accent-bar" style="height: 5px; width: 100%;"></div>

                            <div style="padding: 1.75rem 2rem;">
                                <!-- Header do Card com Logo Institucional Dimensionado -->
                                <div style="text-align: center; margin-bottom: 1.25rem;">
                                    <div id="bks-preview-login-logo-container" style="margin-bottom: 1rem; display: flex; justify-content: center; align-items: center;">
                                        <img id="bks-preview-login-logo" src="/plugins/brandkitslater/pics/logo.png" alt="Logo" style="display: block; max-width: 200px; max-height: 55px; object-fit: contain; transition: all 0.2s ease;">
                                    </div>
                                    <div style="display: inline-flex; align-items: center; gap: 8px; background: #0A1428; padding: 6px 14px; border-radius: 10px; color: #fff; font-size: 11px; font-weight: 700;">
                                        <span style="color: #38bdf8; font-weight: 800;">IT</span>
                                        <span>Portal de Atendimento</span>
                                    </div>
                                    <h4 style="margin: 12px 0 2px 0; font-size: 1.3rem; font-weight: 800; color: #0f172a;">Entrar no Sistema</h4>
                                    <span style="font-size: 11px; color: #64748b;">Acesso corporativo seguro</span>
                                </div>

                                <!-- Campos Mockados -->
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    <div>
                                        <label style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 3px;">Usuário / CPF</label>
                                        <input type="text" placeholder="000.000.000-00" readonly style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 12px; background: #f8fafc;">
                                    </div>

                                    <div>
                                        <label style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #334155; display: block; margin-bottom: 3px;">Senha</label>
                                        <input type="password" value="secretpassword" readonly style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 12px; background: #f8fafc;">
                                    </div>

                                    <button type="button" id="bks-preview-submit-btn" style="
                                        width: 100%;
                                        color: #ffffff;
                                        padding: 10px;
                                        border: none;
                                        border-radius: 8px;
                                        font-weight: 700;
                                        font-size: 12px;
                                        margin-top: 6px;
                                        cursor: pointer;
                                    ">
                                        Acessar Atendimento
                                    </button>
                                </div>

                                <!-- Box de Auto-Cadastro Externo -->
                                <div id="bks-preview-register-box" style="margin-top: 1rem; text-align: center; border-top: 1px solid rgba(0,0,0,0.08); padding-top: 10px;">
                                    <span style="display: block; font-size: 11px; font-weight: 700; color: #003876;">
                                        ➕ Auto-Cadastro de Usuários Externos
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ABA 2: PREVIEW DO PAINEL INTERNO (SIDEBAR & NAVBAR)          -->
                <!-- ============================================================ -->
                <div id="bks-preview-tab-internal" class="brandkit-tab-pane" style="display: none; background: #f8fafc; min-height: 520px; transition: all 0.3s ease;">
                    <!-- Header Superior da Simulação -->
                    <div id="bks-preview-internal-navbar" style="
                        background: #0A1428;
                        border-bottom: 3px solid #C0122D;
                        padding: 0.6rem 1.25rem;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        color: #ffffff;
                        transition: all 0.25s ease;
                    ">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-weight: 800; font-size: 0.85rem; letter-spacing: -0.01em;">GLPI 11 Enterprise</span>
                            <div style="display: flex; gap: 8px;">
                                <span style="background: rgba(255,255,255,0.12); padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 600;">Chamados</span>
                                <span style="background: rgba(255,255,255,0.12); padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 600;">Ativos</span>
                                <span style="background: rgba(255,255,255,0.12); padding: 3px 8px; border-radius: 6px; font-size: 10px; font-weight: 600;">Administração</span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 11px; opacity: 0.85;">🔔</span>
                            <span style="font-size: 11px; opacity: 0.85;">🔍 Busca</span>
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #020617;">AD</div>
                        </div>
                    </div>

                    <!-- Corpo Dividido: Sidebar à Esquerda e Conteúdo à Direita -->
                    <div style="display: flex; min-height: 460px;">
                        <!-- Sidebar Vertical Mockada -->
                        <div id="bks-preview-internal-sidebar" style="
                            width: 220px;
                            background: #0A1428;
                            border-right: 1px solid rgba(0,0,0,0.12);
                            padding: 1rem 0.75rem;
                            display: flex;
                            flex-direction: column;
                            gap: 6px;
                            transition: all 0.25s ease;
                        ">
                            <!-- Logo da Sidebar com Formato, Tamanho e Fit aplicados -->
                            <div style="padding: 0.5rem 0.5rem 1rem 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 0.5rem; display: flex; align-items: center;">
                                <img id="bks-preview-sidebar-logo" src="/plugins/brandkitslater/pics/logo.png" alt="Logo" style="max-width: 140px; max-height: 36px; object-fit: contain; transition: all 0.2s ease;">
                            </div>

                            <!-- Itens de Menu -->
                            <div class="bks-sidebar-item-active" id="bks-preview-sidebar-active-item" style="
                                background: rgba(255,255,255,0.08);
                                color: #38bdf8;
                                border-left: 4px solid #38bdf8;
                                padding: 7px 10px;
                                border-radius: 6px;
                                font-size: 11px;
                                font-weight: 700;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            ">
                                <span>📊</span>
                                <span>Painel Principal</span>
                            </div>

                            <div class="bks-sidebar-item" style="padding: 7px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>🎫</span>
                                <span>Gestão de Chamados</span>
                            </div>

                            <div class="bks-sidebar-item" style="padding: 7px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>💻</span>
                                <span>Parque de Máquinas</span>
                            </div>

                            <div class="bks-sidebar-item" style="padding: 7px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>⚙️</span>
                                <span>Configuração Geral</span>
                            </div>

                            <div class="bks-sidebar-item" style="padding: 7px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #cbd5e1;">
                                <span>🎨</span>
                                <span>BrandKitSlater Studio</span>
                            </div>
                        </div>

                        <!-- Área de Conteúdo da Página Mockada -->
                        <div id="bks-preview-internal-body" style="flex: 1; padding: 1.5rem; background: #f8fafc; transition: all 0.25s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                                <div>
                                    <h5 style="margin: 0; font-size: 1rem; font-weight: 800; color: #0f172a;">Visão Geral do Suporte</h5>
                                    <span style="font-size: 11px; color: #64748b;">Central de Serviços de TI</span>
                                </div>
                                <button type="button" id="bks-preview-internal-btn" style="
                                    background: #003876;
                                    color: #ffffff;
                                    border: none;
                                    padding: 6px 12px;
                                    border-radius: 6px;
                                    font-size: 11px;
                                    font-weight: 700;
                                    cursor: pointer;
                                ">
                                    + Novo Chamado
                                </button>
                            </div>

                            <!-- Cartões de Exemplo do Painel -->
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 1.25rem;">
                                <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Abertos</span>
                                    <div style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin-top: 2px;">14</div>
                                </div>
                                <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Em Andamento</span>
                                    <div style="font-size: 1.3rem; font-weight: 800; color: #0095b7; margin-top: 2px;">8</div>
                                </div>
                                <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Resolvidos</span>
                                    <div style="font-size: 1.3rem; font-weight: 800; color: #10b981; margin-top: 2px;">98.4%</div>
                                </div>
                            </div>

                            <!-- Card de Tabela Mockada -->
                            <div class="bks-preview-subcard" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
                                <span style="font-size: 11px; font-weight: 700; color: #0f172a; display: block; margin-bottom: 8px;">Chamados Recentes</span>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 10px; padding: 4px 0; border-bottom: 1px solid #f1f5f9;">
                                        <span style="font-weight: 600; color: #1e293b;">#1042 - Acesso ao Sistema e VPN Corporativa</span>
                                        <span style="color: #003876; font-weight: 700;">Atribuído</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-size: 10px; padding: 4px 0;">
                                        <span style="font-weight: 600; color: #1e293b;">#1041 - Configuração de Estação de Trabalho</span>
                                        <span style="color: #10b981; font-weight: 700;">Concluído</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ABA 3: PREVIEW DA ABA DO NAVEGADOR & FAVICON                 -->
                <!-- ============================================================ -->
                <div id="bks-preview-tab-browser" class="brandkit-tab-pane" style="display: none; background: #0f172a; min-height: 520px; padding: 1.5rem; transition: all 0.3s ease;">
                    <!-- Janela de Navegador Estilo Chrome / Edge -->
                    <div style="background: #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155;">
                        <!-- Barra de Abas -->
                        <div style="background: #0f172a; padding: 8px 12px 0 12px; display: flex; align-items: center; gap: 8px;">
                            <div style="display: flex; gap: 6px; margin-right: 8px;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                            </div>

                            <!-- Aba Ativa com Favicon e Título -->
                            <div style="
                                background: #1e293b;
                                color: #ffffff;
                                padding: 8px 14px;
                                border-radius: 8px 8px 0 0;
                                font-size: 11px;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                max-width: 240px;
                            ">
                                <img id="bks-preview-favicon-tab" src="/plugins/brandkitslater/pics/favicon.ico" alt="Favicon" style="width: 16px; height: 16px; object-fit: contain; border-radius: 2px;">
                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">GLPI | Central de Atendimento</span>
                                <span style="font-size: 10px; opacity: 0.6; margin-left: auto;">✕</span>
                            </div>

                            <span style="color: #64748b; font-size: 14px; margin-left: 4px; cursor: pointer;">+</span>
                        </div>

                        <!-- Barra de URL / Endereço -->
                        <div style="background: #1e293b; padding: 8px 12px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #334155;">
                            <span style="color: #64748b; font-size: 12px;">← → ⟳</span>
                            <div style="flex: 1; background: #0f172a; padding: 6px 12px; border-radius: 8px; font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 8px; border: 1px solid #334155;">
                                <span style="color: #10b981; font-weight: 800;">🔒 https://</span>
                                <span style="color: #f8fafc; font-weight: 600;">suporte.suaempresa.com.br</span>
                            </div>
                        </div>

                        <!-- Área de Visualização do Favicon em Múltiplas Escalas -->
                        <div style="padding: 2.5rem; text-align: center; background: #1e293b;">
                            <h4 style="color: #f8fafc; margin-top: 0; margin-bottom: 0.5rem; font-size: 1.1rem; font-weight: 800;">🌐 Preview e Escalas do Favicon Institucional</h4>
                            <p style="color: #94a3b8; font-size: 0.82rem; margin-top: 0; margin-bottom: 2rem;">
                                O Favicon é renderizado em várias resoluções pelo navegador (aba, favoritos, atalhos de celular e desktop).
                            </p>

                            <div style="display: flex; justify-content: center; align-items: flex-end; gap: 2rem; flex-wrap: wrap;">
                                <!-- 16x16 -->
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="width: 44px; height: 44px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <img id="bks-preview-fav-16" src="/plugins/brandkitslater/pics/favicon.ico" alt="Favicon 16x16" style="width: 16px; height: 16px; object-fit: contain;">
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">16 × 16 px</span>
                                    <span style="font-size: 9px; color: #64748b;">Aba Web</span>
                                </div>

                                <!-- 32x32 -->
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="width: 56px; height: 56px; background: #0f172a; border: 1px solid #334155; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <img id="bks-preview-fav-32" src="/plugins/brandkitslater/pics/favicon.ico" alt="Favicon 32x32" style="width: 32px; height: 32px; object-fit: contain;">
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">32 × 32 px</span>
                                    <span style="font-size: 9px; color: #64748b;">Favoritos</span>
                                </div>

                                <!-- 64x64 -->
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="width: 88px; height: 88px; background: #0f172a; border: 1px solid #334155; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                        <img id="bks-preview-fav-64" src="/plugins/brandkitslater/pics/favicon.ico" alt="Favicon 64x64" style="width: 64px; height: 64px; object-fit: contain;">
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">64 × 64 px</span>
                                    <span style="font-size: 9px; color: #64748b;">Desktop / Dock</span>
                                </div>

                                <!-- 128x128 -->
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <div style="width: 140px; height: 140px; background: #0f172a; border: 1px solid #334155; border-radius: 18px; display: flex; align-items: center; justify-content: center;">
                                        <img id="bks-preview-fav-128" src="/plugins/brandkitslater/pics/favicon.ico" alt="Favicon 128x128" style="width: 100px; height: 100px; object-fit: contain;">
                                    </div>
                                    <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">128 × 128 px</span>
                                    <span style="font-size: 9px; color: #64748b;">PWA / Mobile</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div style="margin-top: 1rem; text-align: center;">
                <span style="font-size: 0.8rem; color: #64748b;">
                    💡 Dica: Navegue entre as abas acima para pré-visualizar a Tela de Login, o Painel Interno e o Favicon da Aba em tempo real!
                </span>
            </div>
        </div>

    </div>
</div>

<?php
    Html::footer();
} catch (\Throwable $e) {
    if (str_contains(get_class($e), 'Redirect') || (class_exists('Glpi\Exception\RedirectException') && $e instanceof \Glpi\Exception\RedirectException)) {
        throw $e;
    }
    if (!headers_sent()) {
        header('HTTP/1.1 200 OK');
    }
    echo '<div style="margin: 2rem; padding: 2rem; background: #fff1f2; border: 2px solid #e11d48; border-radius: 12px; font-family: monospace; color: #9f1239;">';
    echo '<h3 style="margin-top:0;">⚠️ Detalhe do Erro no BrandKitSlater:</h3>';
    echo '<p><strong>Mensagem:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>Arquivo:</strong> ' . htmlspecialchars($e->getFile()) . ' (linha ' . $e->getLine() . ')</p>';
    echo '<pre style="background: rgba(0,0,0,0.05); padding: 1rem; border-radius: 8px; overflow: auto;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
