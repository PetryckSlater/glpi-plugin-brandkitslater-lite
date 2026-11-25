/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Motor de Live Preview em Tempo Real (Studio Multidimensional)
 * -----------------------------------------------------------------------------
 */

document.addEventListener('DOMContentLoaded', function () {
    const previewContainer = document.getElementById('bks-preview-container');
    const previewCard = document.getElementById('bks-preview-card');
    const previewBtn = document.getElementById('bks-preview-submit-btn');
    const previewAccentBar = document.getElementById('bks-preview-accent-bar');
    const previewOverlay = document.getElementById('bks-preview-overlay');
    const previewRegisterBox = document.getElementById('bks-preview-register-box');
    const previewFrame = document.getElementById('bks-preview-frame');

    if (!previewCard) return;

    // Helper: Aplica formatação de Shape em elemento de imagem
    function applyShapeStyles(el, shape, primaryColor) {
        if (!el) return;
        // Reseta estilos prévios
        el.style.borderRadius = '0';
        el.style.border = 'none';
        el.style.padding = '0';
        el.style.background = 'transparent';
        el.style.boxShadow = 'none';
        el.style.filter = 'none';
        el.style.aspectRatio = 'auto';

        switch (shape) {
            case 'rounded':
                el.style.borderRadius = '12px';
                break;
            case 'circle':
                el.style.borderRadius = '50%';
                el.style.aspectRatio = '1 / 1';
                break;
            case 'pill':
                el.style.borderRadius = '9999px';
                break;
            case 'bordered':
                el.style.border = `2px solid ${primaryColor || '#003876'}`;
                el.style.padding = '5px';
                el.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                el.style.borderRadius = '14px';
                el.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.08)';
                break;
            case 'glow':
                el.style.filter = 'drop-shadow(0 0 10px rgba(0, 149, 183, 0.75))';
                break;
            default:
                // standard
                break;
        }
    }

    // Helper: Sincroniza cor entre input[type=color] e input[type=text]
    function syncColorPair(colorId, textId) {
        const cEl = document.getElementById(colorId);
        const tEl = document.getElementById(textId);
        if (cEl && tEl) {
            cEl.addEventListener('input', function () {
                tEl.value = cEl.value;
            });
            tEl.addEventListener('input', function () {
                if (/^#[0-9A-Fa-f]{6}$/.test(tEl.value)) {
                    cEl.value = tEl.value;
                }
            });
        }
    }

    // Registra sincronização de todos os pares de cor
    syncColorPair('bks_primary_color', 'bks_primary_color_text');
    syncColorPair('bks_primary_hover', 'bks_primary_hover_text');
    syncColorPair('bks_accent_color', 'bks_accent_color_text');
    syncColorPair('bks_navbar_bg', 'bks_navbar_bg_text');
    syncColorPair('bks_navbar_text_color', 'bks_navbar_text_color_text');
    syncColorPair('bks_navbar_border_color', 'bks_navbar_border_color_text');
    syncColorPair('bks_sidebar_bg', 'bks_sidebar_bg_text');
    syncColorPair('bks_sidebar_text_color', 'bks_sidebar_text_color_text');
    syncColorPair('bks_sidebar_active_color', 'bks_sidebar_active_color_text');
    syncColorPair('bks_body_bg', 'bks_body_bg_text');
    syncColorPair('bks_card_bg', 'bks_card_bg_text');

    // Função Principal de Atualização do Preview em Tempo Real
    function updateLivePreview() {
        // Obtenção de Valores dos Controles
        const primary = document.getElementById('bks_primary_color').value || '#003876';
        const primaryHover = document.getElementById('bks_primary_hover').value || '#004ba0';
        const accent = document.getElementById('bks_accent_color').value || '#C0122D';
        const navbarBg = document.getElementById('bks_navbar_bg').value || '#0A1428';
        const navbarTextColor = document.getElementById('bks_navbar_text_color').value || '#ffffff';
        const navbarBorderColor = document.getElementById('bks_navbar_border_color').value || '#C0122D';
        const sidebarBg = document.getElementById('bks_sidebar_bg').value || '#0A1428';
        const sidebarTextColor = document.getElementById('bks_sidebar_text_color').value || '#cbd5e1';
        const sidebarActiveColor = document.getElementById('bks_sidebar_active_color').value || '#38bdf8';
        const sidebarActiveBg = document.getElementById('bks_sidebar_active_bg').value || 'rgba(255,255,255,0.08)';
        const bodyBg = document.getElementById('bks_body_bg').value || '#f8fafc';
        const cardBg = document.getElementById('bks_card_bg').value || '#ffffff';

        const logoShapeRadio = document.querySelector('input[name="logo_shape"]:checked');
        const logoShape = logoShapeRadio ? logoShapeRadio.value : 'standard';
        const logoFit = document.getElementById('bks_logo_fit').value || 'contain';

        const logoUrl = document.getElementById('bks_logo_url').value || '/plugins/brandkitslater/pics/logo.png';
        const faviconUrl = document.getElementById('bks_favicon_url').value || '/plugins/brandkitslater/pics/favicon.ico';

        const sidebarLogoW = document.getElementById('bks_sidebar_logo_width').value || 140;
        const sidebarLogoH = document.getElementById('bks_sidebar_logo_height').value || 36;
        const loginLogoW = document.getElementById('bks_login_logo_width').value || 200;
        const loginLogoH = document.getElementById('bks_login_logo_height').value || 55;

        // -------------------------------------------------------------
        // 1. SINCRONIZAÇÃO DA TELA DE LOGIN (TAB 1)
        // -------------------------------------------------------------
        const posRadio = document.querySelector('input[name="login_position"]:checked');
        const pos = posRadio ? posRadio.value : 'right';

        if (pos === 'right') {
            previewContainer.style.justifyContent = 'flex-end';
            previewContainer.style.paddingRight = '5vw';
            previewContainer.style.paddingLeft = '2rem';
        } else if (pos === 'left') {
            previewContainer.style.justifyContent = 'flex-start';
            previewContainer.style.paddingLeft = '5vw';
            previewContainer.style.paddingRight = '2rem';
        } else {
            previewContainer.style.justifyContent = 'center';
            previewContainer.style.padding = '2rem';
        }

        const cardWidth = document.getElementById('bks_card_width').value || 440;
        const cardRadius = document.getElementById('bks_card_radius').value || 22;
        const cardBlur = document.getElementById('bks_card_blur').value || 24;
        const cardOpacity = (document.getElementById('bks_card_opacity').value || 94) / 100;
        const overlayOpacity = (document.getElementById('bks_bg_overlay_opacity').value || 75) / 100;

        previewCard.style.maxWidth = `${cardWidth}px`;
        previewCard.style.borderRadius = `${cardRadius}px`;

        const cardStyleRadio = document.querySelector('input[name="card_style"]:checked');
        const cardStyle = cardStyleRadio ? cardStyleRadio.value : 'glassmorphism';

        if (cardStyle === 'glassmorphism') {
            previewCard.style.background = `rgba(255, 255, 255, ${cardOpacity})`;
            previewCard.style.backdropFilter = `blur(${cardBlur}px) saturate(190%)`;
            previewCard.style.webkitBackdropFilter = `blur(${cardBlur}px) saturate(190%)`;
            previewCard.style.border = '1px solid rgba(255, 255, 255, 0.65)';
        } else if (cardStyle === 'flat') {
            previewCard.style.background = '#ffffff';
            previewCard.style.backdropFilter = 'none';
            previewCard.style.webkitBackdropFilter = 'none';
            previewCard.style.border = '1px solid #e2e8f0';
        } else {
            previewCard.style.background = '#ffffff';
            previewCard.style.backdropFilter = 'none';
            previewCard.style.webkitBackdropFilter = 'none';
            previewCard.style.border = `2px solid ${primary}`;
        }

        const bgUrl = document.getElementById('bks_bg_image_url').value || '/plugins/brandkitslater/pics/login_bg.jpg';
        previewContainer.style.backgroundImage = `url('${bgUrl}')`;
        previewOverlay.style.background = `linear-gradient(90deg, rgba(10,20,40,0.12) 0%, rgba(10,20,40,0.35) 45%, rgba(10,20,40,${overlayOpacity}) 100%)`;

        previewBtn.style.background = `linear-gradient(135deg, ${primary} 0%, #0A1428 100%)`;
        previewAccentBar.style.background = `linear-gradient(90deg, ${primary} 0%, #0095b7 55%, ${accent} 100%)`;

        const enableRegister = document.getElementById('bks_enable_external_register').checked;
        previewRegisterBox.style.display = enableRegister ? 'block' : 'none';

        const font = document.getElementById('bks_font_family').value || 'Plus Jakarta Sans';
        previewContainer.style.fontFamily = `'${font}', -apple-system, BlinkMacSystemFont, sans-serif`;

        // Logo no Card de Login
        const loginLogoImg = document.getElementById('bks-preview-login-logo');
        if (loginLogoImg) {
            loginLogoImg.src = logoUrl;
            loginLogoImg.style.maxWidth = `${loginLogoW}px`;
            loginLogoImg.style.maxHeight = `${loginLogoH}px`;
            loginLogoImg.style.objectFit = logoFit;
            applyShapeStyles(loginLogoImg, logoShape, primary);
        }

        // -------------------------------------------------------------
        // 2. SINCRONIZAÇÃO DO PAINEL INTERNO (TAB 2)
        // -------------------------------------------------------------
        const internalNavbar = document.getElementById('bks-preview-internal-navbar');
        if (internalNavbar) {
            internalNavbar.style.backgroundColor = navbarBg;
            internalNavbar.style.color = navbarTextColor;
            internalNavbar.style.borderBottom = `3px solid ${navbarBorderColor}`;
        }

        const internalSidebar = document.getElementById('bks-preview-internal-sidebar');
        if (internalSidebar) {
            internalSidebar.style.backgroundColor = sidebarBg;
            // Itens de menu normais
            internalSidebar.querySelectorAll('.bks-sidebar-item').forEach(item => {
                item.style.color = sidebarTextColor;
            });
            // Item ativo
            const activeItem = document.getElementById('bks-preview-sidebar-active-item');
            if (activeItem) {
                activeItem.style.color = sidebarActiveColor;
                activeItem.style.background = sidebarActiveBg;
                activeItem.style.borderLeft = `4px solid ${sidebarActiveColor}`;
            }
        }

        const sidebarLogoImg = document.getElementById('bks-preview-sidebar-logo');
        if (sidebarLogoImg) {
            sidebarLogoImg.src = logoUrl;
            sidebarLogoImg.style.maxWidth = `${sidebarLogoW}px`;
            sidebarLogoImg.style.maxHeight = `${sidebarLogoH}px`;
            sidebarLogoImg.style.objectFit = logoFit;
            applyShapeStyles(sidebarLogoImg, logoShape, primary);
        }

        const internalBody = document.getElementById('bks-preview-internal-body');
        if (internalBody) {
            internalBody.style.backgroundColor = bodyBg;
            internalBody.querySelectorAll('.bks-preview-subcard').forEach(c => {
                c.style.backgroundColor = cardBg;
            });
        }

        const internalBtn = document.getElementById('bks-preview-internal-btn');
        if (internalBtn) {
            internalBtn.style.backgroundColor = primary;
        }

        // -------------------------------------------------------------
        // 3. SINCRONIZAÇÃO DO FAVICON E ABA DO NAVEGADOR (TAB 3)
        // -------------------------------------------------------------
        const favImgs = [
            'bks-preview-favicon-tab',
            'bks-preview-fav-16',
            'bks-preview-fav-32',
            'bks-preview-fav-64',
            'bks-preview-fav-128'
        ];
        favImgs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.src = faviconUrl;
            }
        });

        // -------------------------------------------------------------
        // 4. ATUALIZAÇÃO DOS TEXTOS INDICADORES DOS SLIDERS
        // -------------------------------------------------------------
        document.querySelectorAll('.brandkit-slider-value').forEach(span => {
            const targetId = span.getAttribute('data-target');
            if (targetId && document.getElementById(targetId)) {
                span.textContent = document.getElementById(targetId).value + (span.getAttribute('data-unit') || '');
            }
        });
    }

    // Vincula todos os inputs para atualização reativa
    const allInputs = document.querySelectorAll('#brandkit-config-form input, #brandkit-config-form select, #brandkit-config-form textarea');
    allInputs.forEach(input => {
        input.addEventListener('input', updateLivePreview);
        input.addEventListener('change', updateLivePreview);
    });

    // Seletor visual de Posição (Radio Cards)
    document.querySelectorAll('.brandkit-position-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.brandkit-position-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateLivePreview();
            }
        });
    });

    // Seletor visual de Formato (Shape Cards)
    document.querySelectorAll('.brandkit-shape-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.brandkit-shape-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                updateLivePreview();
            }
        });
    });

    // Alternador de Abas do Preview (Login / Painel Interno / Aba do Navegador)
    document.querySelectorAll('.brandkit-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.brandkit-tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const targetTab = this.getAttribute('data-tab');

            document.querySelectorAll('.brandkit-tab-pane').forEach(pane => {
                pane.style.display = 'none';
            });

            const activePane = document.getElementById(`bks-preview-tab-${targetTab}`);
            if (activePane) {
                activePane.style.display = 'block';
            }
        });
    });

    // Alternador de Dispositivos do Preview (Desktop / Tablet / Mobile)
    document.querySelectorAll('.brandkit-device-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.brandkit-device-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const device = this.getAttribute('data-device');

            if (device === 'mobile') {
                previewFrame.style.maxWidth = '375px';
            } else if (device === 'tablet') {
                previewFrame.style.maxWidth = '768px';
            } else {
                previewFrame.style.maxWidth = '100%';
            }
            updateLivePreview();
        });
    });

    // Carregador de Paletas de Cores de 1 Clique
    window.loadBrandkitColorPreset = function (presetName) {
        const presets = {
            slate: {
                primary: '#1e293b',
                primaryHover: '#334155',
                accent: '#3b82f6',
                navbarBg: '#0f172a',
                navbarText: '#f8fafc',
                navbarBorder: '#3b82f6',
                sidebarBg: '#1e293b',
                sidebarText: '#94a3b8',
                sidebarActive: '#60a5fa',
                sidebarActiveBg: 'rgba(59,130,246,0.15)',
                bodyBg: '#f1f5f9',
                cardBg: '#ffffff',
                shape: 'rounded'
            },
                shape: 'rounded'
            },
            clean: {
                primary: '#2563eb',
                primaryHover: '#1d4ed8',
                accent: '#f59e0b',
                navbarBg: '#ffffff',
                navbarText: '#1e293b',
                navbarBorder: '#2563eb',
                sidebarBg: '#f8fafc',
                sidebarText: '#475569',
                sidebarActive: '#2563eb',
                sidebarActiveBg: '#eff6ff',
                bodyBg: '#f1f5f9',
                cardBg: '#ffffff',
                shape: 'rounded'
            },
            emerald: {
                primary: '#059669',
                primaryHover: '#047857',
                accent: '#f59e0b',
                navbarBg: '#064e3b',
                navbarText: '#ecfdf5',
                navbarBorder: '#10b981',
                sidebarBg: '#064e3b',
                sidebarText: '#a7f3d0',
                sidebarActive: '#34d399',
                sidebarActiveBg: 'rgba(16,185,129,0.15)',
                bodyBg: '#f0fdf4',
                cardBg: '#ffffff',
                shape: 'circle'
            },
            bordeaux: {
                primary: '#881337',
                primaryHover: '#9f1239',
                accent: '#d97706',
                navbarBg: '#4c0519',
                navbarText: '#ffe4e6',
                navbarBorder: '#d97706',
                sidebarBg: '#4c0519',
                sidebarText: '#fecdd3',
                sidebarActive: '#fbbf24',
                sidebarActiveBg: 'rgba(217,119,6,0.18)',
                bodyBg: '#fff1f2',
                cardBg: '#ffffff',
                shape: 'bordered'
            },
            midnight: {
                primary: '#0284c7',
                primaryHover: '#0ea5e9',
                accent: '#38bdf8',
                navbarBg: '#020617',
                navbarText: '#f0f9ff',
                navbarBorder: '#0284c7',
                sidebarBg: '#020617',
                sidebarText: '#94a3b8',
                sidebarActive: '#38bdf8',
                sidebarActiveBg: 'rgba(2,132,199,0.2)',
                bodyBg: '#0b0f19',
                cardBg: '#0f172a',
                shape: 'glow'
            },
            classic: {
                primary: '#204a87',
                primaryHover: '#3465a4',
                accent: '#f57900',
                navbarBg: '#2e3436',
                navbarText: '#eeeeec',
                navbarBorder: '#f57900',
                sidebarBg: '#2e3436',
                sidebarText: '#babdb6',
                sidebarActive: '#729fcf',
                sidebarActiveBg: 'rgba(114,159,207,0.18)',
                bodyBg: '#f3f4f6',
                cardBg: '#ffffff',
                shape: 'standard'
            }
        };

        const p = presets[presetName];
        if (!p) return;

        function setVal(id, val) {
            const el = document.getElementById(id);
            if (el) {
                el.value = val;
                el.dispatchEvent(new Event('input'));
            }
        }

        setVal('bks_primary_color', p.primary);
        setVal('bks_primary_hover', p.primaryHover);
        setVal('bks_accent_color', p.accent);
        setVal('bks_navbar_bg', p.navbarBg);
        setVal('bks_navbar_text_color', p.navbarText);
        setVal('bks_navbar_border_color', p.navbarBorder);
        setVal('bks_sidebar_bg', p.sidebarBg);
        setVal('bks_sidebar_text_color', p.sidebarText);
        setVal('bks_sidebar_active_color', p.sidebarActive);
        setVal('bks_sidebar_active_bg', p.sidebarActiveBg);
        setVal('bks_body_bg', p.bodyBg);
        setVal('bks_card_bg', p.cardBg);

        // Atualiza shape
        const shapeRadio = document.getElementById(`bks_shape_${p.shape}`);
        if (shapeRadio) {
            shapeRadio.checked = true;
            document.querySelectorAll('.brandkit-shape-card').forEach(c => c.classList.remove('active'));
            const parentCard = shapeRadio.closest('.brandkit-shape-card');
            if (parentCard) parentCard.classList.add('active');
        }

        const presetInput = document.getElementById('bks_preset_input');
        if (presetInput) presetInput.value = presetName;

        updateLivePreview();
    };

    // Carregador de Presets Completos
    window.loadBrandkitPreset = function (presetName) {
        if (presetName === 'slate' || presetName === 'slate_glass') {
            loadBrandkitColorPreset('slate');
            document.getElementById('bks_card_width').value = 440;
            document.getElementById('bks_card_radius').value = 22;
            document.getElementById('bks_card_blur').value = 24;
            document.getElementById('bks_card_opacity').value = 94;
            document.getElementById('bks_bg_overlay_opacity').value = 75;
            document.getElementById('bks_bg_image_url').value = '/plugins/brandkitslater/pics/login_bg.jpg';
            document.getElementById('bks_font_family').value = 'Plus Jakarta Sans';
            document.getElementById('bks_enable_external_register').checked = false;
            document.getElementById('bks_pos_right').checked = true;
            document.getElementById('bks_style_glass').checked = true;
            document.getElementById('bks_sidebar_logo_width').value = 140;
            document.getElementById('bks_sidebar_logo_height').value = 36;
            document.getElementById('bks_login_logo_width').value = 200;
            document.getElementById('bks_login_logo_height').value = 55;
        } else if (presetName === 'cyber_dark') {
            loadBrandkitColorPreset('midnight');
            document.getElementById('bks_card_width').value = 420;
            document.getElementById('bks_card_radius').value = 16;
            document.getElementById('bks_card_blur').value = 20;
            document.getElementById('bks_card_opacity').value = 85;
            document.getElementById('bks_bg_overlay_opacity').value = 90;
            document.getElementById('bks_font_family').value = 'Inter';
            document.getElementById('bks_pos_center').checked = true;
            document.getElementById('bks_style_glass').checked = true;
        } else if (presetName === 'corporate_clean') {
            loadBrandkitColorPreset('slate');
            document.getElementById('bks_card_width').value = 400;
            document.getElementById('bks_card_radius').value = 12;
            document.getElementById('bks_font_family').value = 'Inter';
            document.getElementById('bks_pos_center').checked = true;
            document.getElementById('bks_style_flat').checked = true;
        }

        // Atualiza seletores visuais de posição
        document.querySelectorAll('.brandkit-position-card').forEach(c => {
            const r = c.querySelector('input[type="radio"]');
            if (r && r.checked) {
                c.classList.add('active');
            } else {
                c.classList.remove('active');
            }
        });

        updateLivePreview();
    };

    // Executa na inicialização para sincronizar todo o estúdio
    updateLivePreview();
});
