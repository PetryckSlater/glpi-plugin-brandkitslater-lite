/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater v2.2 - Motor Interativo do Studio em Tempo Real
 * Sincroniza Menu Inteiro vs. Menu Recolhido (70px), Tokens GLPI 11,
 * Contraste WCAG, Sliders Dinâmicos e Filtros de Seção
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
    const internalSidebar = document.getElementById('bks-preview-internal-sidebar');
    const toggleFoldBtn = document.getElementById('bks-toggle-fold-btn');
    const simReduceBtn = document.getElementById('bks-sim-reduce-btn');

    if (!previewCard) return;

    let isPreviewMenuCollapsed = false;

    function applyShapeStyles(el, shape, primaryColor, onDarkSurface) {
        if (!el) return;
        el.style.borderRadius = '0';
        el.style.border = 'none';
        el.style.padding = '0';
        el.style.background = 'transparent';
        el.style.boxShadow = 'none';
        el.style.filter = 'none';
        el.style.aspectRatio = 'auto';

        const primary = primaryColor || '#1e293b';

        switch (shape) {
            case 'rounded':
                el.style.borderRadius = '10px';
                break;
            case 'circle':
                el.style.borderRadius = '50%';
                el.style.aspectRatio = '1 / 1';
                el.style.objectFit = 'cover';
                break;
            case 'pill':
                el.style.borderRadius = '9999px';
                el.style.padding = '3px 8px';
                break;
            case 'glass_badge':
                el.style.borderRadius = '12px';
                el.style.padding = '6px';
                el.style.background = onDarkSurface ? 'rgba(255, 255, 255, 0.12)' : 'rgba(15, 23, 42, 0.06)';
                el.style.border = onDarkSurface ? '1px solid rgba(255, 255, 255, 0.22)' : '1px solid rgba(15, 23, 42, 0.12)';
                el.style.boxShadow = onDarkSurface ? '0 4px 12px rgba(0,0,0,0.2)' : 'none';
                break;
            case 'bordered':
                el.style.border = onDarkSurface ? '1.5px solid rgba(255,255,255,0.35)' : `2px solid ${primary}`;
                el.style.padding = '5px';
                el.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                el.style.borderRadius = '12px';
                el.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.12)';
                break;
            case 'glow':
                el.style.filter = 'drop-shadow(0 0 10px rgba(56, 189, 248, 0.65))';
                break;
            default:
                break;
        }
    }

    function syncColorPair(colorId, textId) {
        const cEl = document.getElementById(colorId);
        const tEl = document.getElementById(textId);
        if (cEl && tEl) {
            cEl.addEventListener('input', function () {
                tEl.value = cEl.value;
            });
            tEl.addEventListener('input', function () {
                if (/^#[0-9A-Fa-f]{6}$/.test(tEl.value.trim())) {
                    cEl.value = tEl.value.trim();
                    updateLivePreview();
                }
            });
        }
    }

    [
        ['bks_primary_color', 'bks_primary_color_text'],
        ['bks_primary_hover', 'bks_primary_hover_text'],
        ['bks_accent_color', 'bks_accent_color_text'],
        ['bks_navbar_bg', 'bks_navbar_bg_text'],
        ['bks_navbar_text_color', 'bks_navbar_text_color_text'],
        ['bks_navbar_border_color', 'bks_navbar_border_color_text'],
        ['bks_sidebar_bg', 'bks_sidebar_bg_text'],
        ['bks_sidebar_text_color', 'bks_sidebar_text_color_text'],
        ['bks_sidebar_active_color', 'bks_sidebar_active_color_text'],
        ['bks_body_bg', 'bks_body_bg_text'],
        ['bks_card_bg', 'bks_card_bg_text']
    ].forEach(([c, t]) => syncColorPair(c, t));

    function getLuminance(hex) {
        if (!hex || hex.length < 6) return 0.5;
        const clean = hex.replace('#', '');
        if (clean.length !== 6) return 0.5;
        const r = parseInt(clean.substr(0, 2), 16) / 255;
        const g = parseInt(clean.substr(2, 2), 16) / 255;
        const b = parseInt(clean.substr(4, 2), 16) / 255;
        const a = [r, g, b].map(v => (v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4)));
        return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
    }

    function updateContrastBadge(btnColor) {
        const badge = document.getElementById('bks-contrast-badge');
        if (!badge) return;
        const l1 = getLuminance('#ffffff');
        const l2 = getLuminance(btnColor || '#1e293b');
        const ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
        const ratioFormatted = ratio.toFixed(1) + ':1';

        if (ratio >= 7.0) {
            badge.className = 'brandkit-contrast-badge';
            badge.textContent = `✨ WCAG AAA (${ratioFormatted})`;
        } else if (ratio >= 4.5) {
            badge.className = 'brandkit-contrast-badge';
            badge.textContent = `✅ WCAG AA (${ratioFormatted})`;
        } else {
            badge.className = 'brandkit-contrast-badge warn';
            badge.textContent = `⚠️ Contraste (${ratioFormatted})`;
        }
    }

    function syncAllSlidersProgress(fillColor) {
        document.querySelectorAll('.brandkit-slider-group input[type="range"]').forEach(slider => {
            const min = parseFloat(slider.min) || 0;
            const max = parseFloat(slider.max) || 100;
            const val = parseFloat(slider.value) || 0;
            const progress = ((val - min) / (max - min)) * 100;
            slider.style.setProperty('--slider-progress', `${progress}%`);
            if (fillColor) {
                slider.style.setProperty('--slider-fill-color', fillColor);
            }
        });
    }

    function switchPreviewTab(targetTab) {
        document.querySelectorAll('.brandkit-tab-btn').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-tab') === targetTab);
        });
        document.querySelectorAll('.brandkit-tab-pane').forEach(pane => {
            pane.style.display = 'none';
        });
        const activePane = document.getElementById(`bks-preview-tab-${targetTab}`);
        if (activePane) {
            activePane.style.display = 'block';
        }
    }

    function syncPreviewCollapsedUI() {
        if (!internalSidebar) return;
        const expLogo = document.getElementById('bks-preview-sidebar-logo');
        const colLogo = document.getElementById('bks-preview-sidebar-logo-collapsed');
        const arrow = document.getElementById('bks-sim-reduce-arrow');
        const sidebarWidthVal = (document.getElementById('bks_sidebar_width')?.value || 240);

        internalSidebar.classList.toggle('is-collapsed', isPreviewMenuCollapsed);

        if (isPreviewMenuCollapsed) {
            internalSidebar.style.width = '70px';
            if (expLogo) expLogo.style.display = 'none';
            if (colLogo) colLogo.style.display = 'block';
            if (arrow) arrow.textContent = '»';
            if (toggleFoldBtn) {
                toggleFoldBtn.classList.add('is-collapsed');
                toggleFoldBtn.innerHTML = '<span>📖 Voltar ao Menu Inteiro (Expandido)</span>';
            }
        } else {
            internalSidebar.style.width = `${Math.round(sidebarWidthVal * 0.9)}px`;
            if (expLogo) expLogo.style.display = 'block';
            if (colLogo) colLogo.style.display = 'none';
            if (arrow) arrow.textContent = '«';
            if (toggleFoldBtn) {
                toggleFoldBtn.classList.remove('is-collapsed');
                toggleFoldBtn.innerHTML = '<span>📐 Testar Menu Recolhido (70px)</span>';
            }
        }
    }

    function togglePreviewCollapsed() {
        isPreviewMenuCollapsed = !isPreviewMenuCollapsed;
        syncPreviewCollapsedUI();
    }

    if (toggleFoldBtn) toggleFoldBtn.addEventListener('click', togglePreviewCollapsed);
    if (simReduceBtn) simReduceBtn.addEventListener('click', togglePreviewCollapsed);

    function updateLivePreview() {
        const primary = document.getElementById('bks_primary_color')?.value || '#1e293b';
        const accent = document.getElementById('bks_accent_color')?.value || '#6366f1';
        const navbarBg = document.getElementById('bks_navbar_bg')?.value || '#0A1428';
        const navbarTextColor = document.getElementById('bks_navbar_text_color')?.value || '#ffffff';
        const navbarBorderColor = document.getElementById('bks_navbar_border_color')?.value || '#6366f1';
        const sidebarBg = document.getElementById('bks_sidebar_bg')?.value || '#0A1428';
        const sidebarTextColor = document.getElementById('bks_sidebar_text_color')?.value || '#cbd5e1';
        const sidebarActiveColor = document.getElementById('bks_sidebar_active_color')?.value || '#38bdf8';
        const sidebarActiveBg = document.getElementById('bks_sidebar_active_bg')?.value || 'rgba(255,255,255,0.08)';
        const bodyBg = document.getElementById('bks_body_bg')?.value || '#f8fafc';
        const cardBg = document.getElementById('bks_card_bg')?.value || '#ffffff';
        const uiRadius = document.getElementById('bks_ui_border_radius')?.value || 10;
        const internalCardStyle = document.getElementById('bks_internal_card_style')?.value || 'elevated';
        const sidebarItemStyle = document.getElementById('bks_sidebar_item_style')?.value || 'pill';

        const logoShapeRadio = document.querySelector('input[name="logo_shape"]:checked');
        const loginLogoShape = logoShapeRadio ? logoShapeRadio.value : 'standard';
        const logoFit = document.getElementById('bks_logo_fit')?.value || 'contain';

        const mainLogoUrl = document.getElementById('bks_logo_url')?.value || '/plugins/brandkitslater/pics/logo.svg';
        const rawSideLogoUrl = document.getElementById('bks_sidebar_logo_url')?.value?.trim() || '';
        const effectiveSideLogoUrl = rawSideLogoUrl !== '' ? rawSideLogoUrl : mainLogoUrl;
        const collapsedLogoUrl = document.getElementById('bks_collapsed_logo_url')?.value?.trim() || '/plugins/brandkitslater/pics/logo_collapsed.svg';
        const faviconUrl = document.getElementById('bks_favicon_url')?.value || '/plugins/brandkitslater/pics/favicon.ico';

        const sidebarLogoW = document.getElementById('bks_sidebar_logo_width')?.value || 145;
        const sidebarLogoH = document.getElementById('bks_sidebar_logo_height')?.value || 40;
        const sidebarLogoShape = document.getElementById('bks_sidebar_logo_shape')?.value || 'standard';
        const sidebarLogoBgPill = document.getElementById('bks_sidebar_logo_bg_pill')?.checked || false;

        const collapsedSize = document.getElementById('bks_collapsed_logo_size')?.value || 38;
        const collapsedShape = document.getElementById('bks_collapsed_logo_shape')?.value || 'rounded';

        const loginLogoW = document.getElementById('bks_login_logo_width')?.value || 220;
        const loginLogoH = document.getElementById('bks_login_logo_height')?.value || 60;

        // 1. Atualiza Mini-Palcos de Comparação (Menu Inteiro vs Menu Recolhido)
        const stageExpanded = document.getElementById('bks-stage-expanded');
        const stageExpandedImg = document.getElementById('bks-stage-expanded-img');
        const stageCollapsed = document.getElementById('bks-stage-collapsed');
        const stageCollapsedImg = document.getElementById('bks-stage-collapsed-img');

        if (stageExpanded) stageExpanded.style.backgroundColor = sidebarBg;
        if (stageCollapsed) stageCollapsed.style.backgroundColor = sidebarBg;

        if (stageExpandedImg) {
            stageExpandedImg.src = effectiveSideLogoUrl;
            stageExpandedImg.style.maxWidth = `${sidebarLogoW}px`;
            stageExpandedImg.style.maxHeight = `${sidebarLogoH}px`;
            stageExpandedImg.style.objectFit = logoFit;
            applyShapeStyles(stageExpandedImg, sidebarLogoShape, primary, true);
            if (sidebarLogoBgPill) {
                stageExpandedImg.style.background = 'rgba(255,255,255,0.94)';
                stageExpandedImg.style.padding = '5px 10px';
                stageExpandedImg.style.borderRadius = '10px';
            }
        }

        if (stageCollapsedImg) {
            stageCollapsedImg.src = collapsedLogoUrl;
            stageCollapsedImg.style.width = `${collapsedSize}px`;
            stageCollapsedImg.style.height = `${collapsedSize}px`;
            stageCollapsedImg.style.objectFit = logoFit;
            applyShapeStyles(stageCollapsedImg, collapsedShape, primary, true);
        }

        // 2. Sincroniza Aba 1 (Tela de Login)
        const posRadio = document.querySelector('input[name="login_position"]:checked');
        const pos = posRadio ? posRadio.value : 'right';

        if (pos === 'right') {
            previewContainer.style.justifyContent = 'flex-end';
            previewContainer.style.paddingRight = '4vw';
            previewContainer.style.paddingLeft = '1.5rem';
        } else if (pos === 'left') {
            previewContainer.style.justifyContent = 'flex-start';
            previewContainer.style.paddingLeft = '4vw';
            previewContainer.style.paddingRight = '1.5rem';
        } else {
            previewContainer.style.justifyContent = 'center';
            previewContainer.style.padding = '1.5rem';
        }

        const cardWidth = document.getElementById('bks_card_width')?.value || 440;
        const cardRadius = document.getElementById('bks_card_radius')?.value || 22;
        const cardBlur = document.getElementById('bks_card_blur')?.value || 24;
        const cardOpacity = (document.getElementById('bks_card_opacity')?.value || 94) / 100;
        const overlayOpacity = (document.getElementById('bks_bg_overlay_opacity')?.value || 75) / 100;

        previewCard.style.maxWidth = `${Math.min(cardWidth, 440)}px`;
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
            previewCard.style.border = '1px solid #e2e8f0';
        } else {
            previewCard.style.background = '#ffffff';
            previewCard.style.backdropFilter = 'none';
            previewCard.style.border = `2px solid ${primary}`;
        }

        const bgUrl = document.getElementById('bks_bg_image_url')?.value || '/plugins/brandkitslater/pics/login_bg.jpg';
        previewContainer.style.backgroundImage = `url('${bgUrl}')`;
        previewOverlay.style.background = `linear-gradient(90deg, rgba(10,20,40,0.12) 0%, rgba(10,20,40,0.35) 45%, rgba(10,20,40,${overlayOpacity}) 100%)`;

        previewBtn.style.background = `linear-gradient(135deg, ${primary} 0%, #0A1428 100%)`;
        previewAccentBar.style.background = `linear-gradient(90deg, ${primary} 0%, #0095b7 55%, ${accent} 100%)`;

        // Textos dinâmicos do Login
        const badgeText = document.getElementById('bks_login_badge_text')?.value ?? '';
        const loginBadgeEl = document.getElementById('bks-preview-login-badge');
        if (loginBadgeEl) {
            if (badgeText.trim() === '') {
                loginBadgeEl.style.display = 'none';
            } else {
                loginBadgeEl.style.display = 'inline-flex';
                loginBadgeEl.textContent = badgeText;
            }
        }

        const headingEl = document.getElementById('bks-preview-login-heading');
        if (headingEl) headingEl.textContent = document.getElementById('bks_login_title')?.value || 'Entrar no Sistema';

        const subEl = document.getElementById('bks-preview-login-subtitle');
        if (subEl) subEl.textContent = document.getElementById('bks_login_subtitle')?.value || 'Acesso corporativo seguro';

        if (previewBtn) previewBtn.textContent = document.getElementById('bks_login_btn_text')?.value || 'Acessar Atendimento';

        const enableRegister = document.getElementById('bks_enable_external_register')?.checked;
        if (previewRegisterBox) previewRegisterBox.style.display = enableRegister ? 'block' : 'none';
        const regTextEl = document.getElementById('bks-preview-register-text');
        if (regTextEl) {
            regTextEl.textContent = '➕ ' + (document.getElementById('bks_external_register_text')?.value || 'Auto-Cadastro de Usuários Externos');
            regTextEl.style.color = primary;
        }

        const font = document.getElementById('bks_font_family')?.value || 'Plus Jakarta Sans';
        previewContainer.style.fontFamily = `'${font}', -apple-system, BlinkMacSystemFont, sans-serif`;

        const loginLogoImg = document.getElementById('bks-preview-login-logo');
        if (loginLogoImg) {
            loginLogoImg.src = mainLogoUrl;
            loginLogoImg.style.maxWidth = `${loginLogoW}px`;
            loginLogoImg.style.maxHeight = `${loginLogoH}px`;
            loginLogoImg.style.objectFit = logoFit;
            applyShapeStyles(loginLogoImg, loginLogoShape, primary, false);
        }

        // 3. Sincroniza Aba 2 (Painel Interno GLPI + Menu Inteiro e Recolhido)
        const internalNavbar = document.getElementById('bks-preview-internal-navbar');
        if (internalNavbar) {
            internalNavbar.style.backgroundColor = navbarBg;
            internalNavbar.style.color = navbarTextColor;
            internalNavbar.style.borderBottom = `3px solid ${navbarBorderColor}`;
        }

        if (internalSidebar) {
            internalSidebar.style.backgroundColor = sidebarBg;
            internalSidebar.querySelectorAll('.bks-sidebar-item').forEach(item => {
                item.style.color = sidebarTextColor;
                item.style.borderRadius = sidebarItemStyle === 'native' ? '0px' : `${uiRadius}px`;
            });

            const activeItem = document.getElementById('bks-preview-sidebar-active-item');
            if (activeItem) {
                activeItem.style.color = sidebarActiveColor;
                activeItem.style.background = sidebarActiveBg;
                activeItem.style.borderRadius = sidebarItemStyle === 'native' ? '0px' : `${uiRadius}px`;
                activeItem.style.borderLeft = sidebarItemStyle === 'glass'
                    ? '1px solid rgba(255,255,255,0.22)'
                    : `3px solid ${sidebarActiveColor}`;
            }

            if (simReduceBtn) {
                simReduceBtn.style.color = sidebarTextColor;
            }
        }

        const sidebarLogoImg = document.getElementById('bks-preview-sidebar-logo');
        if (sidebarLogoImg) {
            sidebarLogoImg.src = effectiveSideLogoUrl;
            sidebarLogoImg.style.maxWidth = `${sidebarLogoW}px`;
            sidebarLogoImg.style.maxHeight = `${sidebarLogoH}px`;
            sidebarLogoImg.style.objectFit = logoFit;
            applyShapeStyles(sidebarLogoImg, sidebarLogoShape, primary, true);
            if (sidebarLogoBgPill) {
                sidebarLogoImg.style.background = 'rgba(255,255,255,0.94)';
                sidebarLogoImg.style.padding = '5px 10px';
                sidebarLogoImg.style.borderRadius = '10px';
            }
        }

        const sidebarCollapsedImg = document.getElementById('bks-preview-sidebar-logo-collapsed');
        if (sidebarCollapsedImg) {
            sidebarCollapsedImg.src = collapsedLogoUrl;
            sidebarCollapsedImg.style.width = `${collapsedSize}px`;
            sidebarCollapsedImg.style.height = `${collapsedSize}px`;
            sidebarCollapsedImg.style.objectFit = logoFit;
            applyShapeStyles(sidebarCollapsedImg, collapsedShape, primary, true);
        }

        syncPreviewCollapsedUI();

        const internalBody = document.getElementById('bks-preview-internal-body');
        if (internalBody) {
            internalBody.style.backgroundColor = bodyBg;
            internalBody.style.fontFamily = `'${font}', sans-serif`;
            internalBody.querySelectorAll('.bks-preview-subcard').forEach(c => {
                c.style.backgroundColor = cardBg;
                c.style.borderRadius = `${uiRadius}px`;
                c.style.boxShadow = internalCardStyle === 'native' ? 'none' : '0 4px 14px -4px rgba(15,23,42,0.08)';
                c.style.borderTop = '1px solid #e2e8f0';
            });

            const mainPanel = document.getElementById('bks-preview-main-panel');
            if (mainPanel && internalCardStyle === 'accent_top') {
                mainPanel.style.borderTop = `3px solid ${primary}`;
            }
        }

        const internalBtn = document.getElementById('bks-preview-internal-btn');
        if (internalBtn) {
            internalBtn.style.backgroundColor = primary;
            internalBtn.style.borderRadius = `${uiRadius}px`;
        }

        // 4. Sincroniza Aba 3 (Favicon & Marca da Aba)
        ['bks-preview-favicon-tab', 'bks-preview-fav-16', 'bks-preview-fav-32', 'bks-preview-fav-64'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.src = faviconUrl;
        });

        const tabBrand = document.getElementById('bks_page_title_brand')?.value?.trim() || '';
        const tabTitleEl = document.getElementById('bks-preview-tab-title-text');
        if (tabTitleEl) {
            tabTitleEl.textContent = tabBrand !== '' ? `${tabBrand} • Assistência` : 'GLPI • Central de Atendimento';
        }

        // 5. Indicadores numéricos dos Sliders + Progresso + Contraste WCAG
        document.querySelectorAll('.brandkit-slider-value').forEach(span => {
            const targetId = span.getAttribute('data-target');
            const targetEl = targetId ? document.getElementById(targetId) : null;
            if (targetEl) {
                span.textContent = targetEl.value + (span.getAttribute('data-unit') || '');
            }
        });

        syncAllSlidersProgress(primary);
        updateContrastBadge(primary);
    }

    // Filtro de Seções do Studio
    document.querySelectorAll('.brandkit-section-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.brandkit-section-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const filter = this.getAttribute('data-section-filter');
            const jumpTab = this.getAttribute('data-jump-tab');

            document.querySelectorAll('.brandkit-card[data-bks-section]').forEach(card => {
                if (filter === 'all' || card.getAttribute('data-bks-section') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });

            if (jumpTab) {
                switchPreviewTab(jumpTab);
            }
        });
    });

    // Inputs reativos
    document.querySelectorAll('#brandkit-config-form input, #brandkit-config-form select, #brandkit-config-form textarea').forEach(input => {
        input.addEventListener('input', updateLivePreview);
        input.addEventListener('change', updateLivePreview);
    });

    // Radio Cards de Posição e Formato
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

    // Abas do Preview
    document.querySelectorAll('.brandkit-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            switchPreviewTab(this.getAttribute('data-tab'));
        });
    });

    // Alternador de Viewport (Desktop / Tablet / Mobile)
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

    // Paletas Rápidas de 1 Clique
    window.loadBrandkitColorPreset = function (presetName) {
        const presets = {
            slate: {
                primary: '#1e293b', primaryHover: '#334155', accent: '#6366f1',
                navbarBg: '#0f172a', navbarText: '#f8fafc', navbarBorder: '#6366f1',
                sidebarBg: '#1e293b', sidebarText: '#94a3b8', sidebarActive: '#818cf8',
                sidebarActiveBg: 'rgba(99,102,241,0.16)', bodyBg: '#f1f5f9', cardBg: '#ffffff', shape: 'rounded'
            },
            clean: {
                primary: '#2563eb', primaryHover: '#1d4ed8', accent: '#f59e0b',
                navbarBg: '#ffffff', navbarText: '#1e293b', navbarBorder: '#2563eb',
                sidebarBg: '#0f172a', sidebarText: '#cbd5e1', sidebarActive: '#60a5fa',
                sidebarActiveBg: 'rgba(59,130,246,0.18)', bodyBg: '#f8fafc', cardBg: '#ffffff', shape: 'rounded'
            },
            emerald: {
                primary: '#059669', primaryHover: '#047857', accent: '#f59e0b',
                navbarBg: '#064e3b', navbarText: '#ecfdf5', navbarBorder: '#10b981',
                sidebarBg: '#064e3b', sidebarText: '#a7f3d0', sidebarActive: '#34d399',
                sidebarActiveBg: 'rgba(16,185,129,0.18)', bodyBg: '#f0fdf4', cardBg: '#ffffff', shape: 'rounded'
            },
            bordeaux: {
                primary: '#881337', primaryHover: '#9f1239', accent: '#d97706',
                navbarBg: '#4c0519', navbarText: '#ffe4e6', navbarBorder: '#d97706',
                sidebarBg: '#4c0519', sidebarText: '#fecdd3', sidebarActive: '#fbbf24',
                sidebarActiveBg: 'rgba(217,119,6,0.18)', bodyBg: '#fff1f2', cardBg: '#ffffff', shape: 'bordered'
            },
            midnight: {
                primary: '#0284c7', primaryHover: '#0ea5e9', accent: '#38bdf8',
                navbarBg: '#020617', navbarText: '#f0f9ff', navbarBorder: '#0284c7',
                sidebarBg: '#020617', sidebarText: '#94a3b8', sidebarActive: '#38bdf8',
                sidebarActiveBg: 'rgba(2,132,199,0.2)', bodyBg: '#0b0f19', cardBg: '#0f172a', shape: 'glow'
            },
            classic: {
                primary: '#2f3f64', primaryHover: '#3d517f', accent: '#fec95c',
                navbarBg: '#ffffff', navbarText: '#1e293b', navbarBorder: '#e2e8f0',
                sidebarBg: '#2f3f64', sidebarText: '#f4f6fa', sidebarActive: '#fec95c',
                sidebarActiveBg: 'rgba(255,255,255,0.12)', bodyBg: '#f4f6fa', cardBg: '#ffffff', shape: 'standard'
            }
        };

        const p = presets[presetName];
        if (!p) return;

        const map = {
            bks_primary_color: p.primary,
            bks_primary_hover: p.primaryHover,
            bks_accent_color: p.accent,
            bks_navbar_bg: p.navbarBg,
            bks_navbar_text_color: p.navbarText,
            bks_navbar_border_color: p.navbarBorder,
            bks_sidebar_bg: p.sidebarBg,
            bks_sidebar_text_color: p.sidebarText,
            bks_sidebar_active_color: p.sidebarActive,
            bks_sidebar_active_bg: p.sidebarActiveBg,
            bks_body_bg: p.bodyBg,
            bks_card_bg: p.cardBg
        };

        Object.entries(map).forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el) {
                el.value = val;
                el.dispatchEvent(new Event('input'));
            }
        });

        const shapeRadio = document.getElementById(`bks_shape_${p.shape}`);
        if (shapeRadio) {
            shapeRadio.checked = true;
            document.querySelectorAll('.brandkit-shape-card').forEach(c => c.classList.remove('active'));
            shapeRadio.closest('.brandkit-shape-card')?.classList.add('active');
        }

        const presetInput = document.getElementById('bks_preset_input');
        if (presetInput) presetInput.value = presetName;
        updateLivePreview();
    };

    window.loadBrandkitPreset = function (presetName) {
        if (presetName === 'cyber_dark') {
            loadBrandkitColorPreset('midnight');
            document.getElementById('bks_card_width').value = 420;
            document.getElementById('bks_card_radius').value = 18;
            document.getElementById('bks_card_blur').value = 24;
            document.getElementById('bks_card_opacity').value = 88;
            document.getElementById('bks_bg_overlay_opacity').value = 88;
            document.getElementById('bks_font_family').value = 'Inter';
            document.getElementById('bks_pos_center').checked = true;
            document.getElementById('bks_style_glass').checked = true;
        } else if (presetName === 'corporate_clean') {
            loadBrandkitColorPreset('slate');
            document.getElementById('bks_card_width').value = 420;
            document.getElementById('bks_card_radius').value = 16;
            document.getElementById('bks_font_family').value = 'Plus Jakarta Sans';
            document.getElementById('bks_pos_right').checked = true;
            document.getElementById('bks_style_glass').checked = true;
        }

        document.querySelectorAll('.brandkit-position-card').forEach(c => {
            const r = c.querySelector('input[type="radio"]');
            c.classList.toggle('active', Boolean(r && r.checked));
        });

        updateLivePreview();
    };

    updateLivePreview();
});
