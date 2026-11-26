/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater v2.2 - Motor Frontend de Identidade Visual para GLPI
 * Gerencia Favicon, Título da Aba, Tela de Login e Troca Reativa entre
 * Logo do Menu Inteiro (Expandido) e Ícone do Menu Recolhido (70px)
 * -----------------------------------------------------------------------------
 */
(function () {
    'use strict';

    function getShapeInlineStyle(shape, primaryColor, onDarkSurface) {
        const primary = primaryColor || '#003876';
        switch (shape) {
            case 'rounded':
                return 'border-radius: 10px;';
            case 'circle':
                return 'border-radius: 50%; aspect-ratio: 1 / 1; object-fit: cover;';
            case 'pill':
                return 'border-radius: 9999px; padding: 3px 8px;';
            case 'glass_badge':
                return onDarkSurface
                    ? 'border-radius: 12px; padding: 6px; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.22); backdrop-filter: blur(8px); box-shadow: 0 4px 12px rgba(0,0,0,0.2);'
                    : 'border-radius: 12px; padding: 6px; background: rgba(15, 23, 42, 0.06); border: 1px solid rgba(15, 23, 42, 0.12);';
            case 'bordered':
                return onDarkSurface
                    ? 'border: 1.5px solid rgba(255, 255, 255, 0.35); padding: 5px; background-color: rgba(255, 255, 255, 0.95); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.18);'
                    : `border: 2px solid ${primary}; padding: 6px; background-color: rgba(255, 255, 255, 0.96); border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);`;
            case 'glow':
                return 'filter: drop-shadow(0 0 10px rgba(56, 189, 248, 0.6));';
            default:
                return '';
        }
    }

    function resolveImageWithFallback(candidates, onResolved) {
        const list = candidates.filter(Boolean);
        let idx = 0;

        function tryNext() {
            if (idx >= list.length) return;
            const current = list[idx++];
            const probe = new Image();
            probe.onload = function () {
                onResolved(current);
            };
            probe.onerror = tryNext;
            probe.src = current;
        }

        tryNext();
    }

    function initBrandkitIcons() {
        const bData = window.BRANDKIT_DATA || {};
        const timestamp = Date.now();
        const primaryColor = bData.primary_color || '#003876';

        // 1. Personalização opcional do Título da Aba do Navegador (<title>)
        if (bData.page_title_brand && typeof bData.page_title_brand === 'string' && bData.page_title_brand.trim() !== '') {
            const brandTitle = bData.page_title_brand.trim();
            if (document.title.includes('GLPI')) {
                document.title = document.title.replace(/GLPI/g, brandTitle);
            } else if (!document.title.includes(brandTitle)) {
                document.title = `${brandTitle} • ${document.title}`;
            }
        }

        // 2. Aplicação Dinâmica de Favicon Customizado
        resolveImageWithFallback(
            [
                bData.favicon_url,
                '/plugins/brandkitslater/pics/favicon.ico?v=' + timestamp,
                '/plugins/brandkitslater/pics/favicon.png?v=' + timestamp,
                '/plugins/brandkitslater/pics/favicon.svg?v=' + timestamp
            ],
            function (validFavicon) {
                const links = document.querySelectorAll("link[rel*='icon']");
                if (links.length > 0) {
                    links.forEach(l => {
                        l.href = validFavicon;
                    });
                } else {
                    const link = document.createElement('link');
                    link.rel = 'icon';
                    link.href = validFavicon;
                    document.head.appendChild(link);
                }
            }
        );

        // 3. Harmonização da Tela de Login (Textos Customizáveis + Logotipo do Card)
        const isLoginPage = Boolean(document.querySelector('body.welcome-anonymous, body.page-anonymous, form[action*="login"]'));
        if (isLoginPage) {
            const mainHeading = document.querySelector('.card-body .card-header h2, .card-body h2.mx-auto');
            if (mainHeading && !document.querySelector('[data-code-type], input[name="totp_code[]"]')) {
                const customTitle = (bData.login_title !== undefined && bData.login_title !== '') ? bData.login_title : 'Entrar no Sistema';
                if (mainHeading.textContent.toLowerCase().includes('login') || mainHeading.textContent.toLowerCase().includes('entrar')) {
                    mainHeading.textContent = customTitle;
                }
            }

            const richContainer = document.querySelector('.card-body .rich_text_container');
            if (richContainer && bData.login_subtitle !== '') {
                const customSubtitle = bData.login_subtitle || 'Acesso corporativo seguro';
                richContainer.innerHTML = '';
                const subSpan = document.createElement('span');
                subSpan.style.cssText = 'font-size: 0.88rem; color: #64748b; font-weight: 500; display: block; margin-top: -4px; margin-bottom: 1.25rem; text-align: center;';
                subSpan.textContent = customSubtitle;
                richContainer.appendChild(subSpan);
            }

            const submitBtn = document.querySelector('.card-body button[type="submit"]');
            if (submitBtn && !document.querySelector('[data-code-type], input[name="totp_code[]"]')) {
                const customBtnText = bData.login_btn_text || 'Acessar Atendimento';
                if (submitBtn.textContent.trim().toLowerCase().includes('entrar') || submitBtn.textContent.trim().toLowerCase().includes('sign in')) {
                    submitBtn.textContent = customBtnText;
                }
            }

            resolveImageWithFallback(
                [
                    bData.logo_url,
                    '/plugins/brandkitslater/pics/logo.png?v=' + timestamp,
                    '/plugins/brandkitslater/pics/logo.svg?v=' + timestamp
                ],
                function (validLoginLogo) {
                    const cardBody = document.querySelector('.card.card-md .card-body, .card .card-body');
                    if (!cardBody) return;

                    let img = cardBody.querySelector('.brandkit-card-logo');
                    if (!img) {
                        img = document.createElement('img');
                        img.className = 'brandkit-card-logo';
                        cardBody.insertBefore(img, cardBody.firstChild);
                    }

                    const loginW = bData.login_logo_width || 220;
                    const loginH = bData.login_logo_height || 60;
                    const fit = bData.logo_fit || 'contain';
                    const shape = bData.logo_shape || 'standard';

                    img.src = validLoginLogo;
                    img.alt = bData.page_title_brand || 'Logotipo Institucional';
                    img.style.cssText = `display: block; max-width: ${loginW}px; max-height: ${loginH}px; width: auto; height: auto; margin: 0 auto 1.25rem auto; object-fit: ${fit}; transition: all 0.25s ease; ${getShapeInlineStyle(shape, primaryColor, false)}`;
                }
            );
            return;
        }

        // 4. Motor Dual de Logotipos no Painel Interno do GLPI (Menu Inteiro vs. Menu Recolhido)
        if (bData.enable_custom_logos === 0) {
            return;
        }

        const sidebarBrand = document.querySelector('aside.navbar-vertical .navbar-brand, .navbar-vertical .navbar-brand');
        const topbarBrand = document.querySelector('header.topbar .navbar-brand, body.horizontal-layout header.navbar .navbar-brand');

        const expandedCandidates = [
            bData.sidebar_logo_url,
            bData.logo_url,
            '/plugins/brandkitslater/pics/logo.png?v=' + timestamp,
            '/plugins/brandkitslater/pics/logo.svg?v=' + timestamp
        ];

        const collapsedCandidates = [
            bData.collapsed_logo_url,
            '/plugins/brandkitslater/pics/logo_collapsed.svg?v=' + timestamp,
            bData.favicon_url,
            '/plugins/brandkitslater/pics/favicon.png?v=' + timestamp,
            bData.logo_url
        ];

        // 4a. Barra Lateral Vertical (Suporta Expansão e Recolhimento 70px em Tempo Real!)
        if (sidebarBrand) {
            let expandedImg = sidebarBrand.querySelector('.brandkit-logo-expanded');
            if (!expandedImg) {
                expandedImg = document.createElement('img');
                expandedImg.className = 'brandkit-sidebar-logo brandkit-logo-expanded';
                sidebarBrand.prepend(expandedImg);
            }

            let collapsedImg = sidebarBrand.querySelector('.brandkit-logo-collapsed');
            if (!collapsedImg) {
                collapsedImg = document.createElement('img');
                collapsedImg.className = 'brandkit-sidebar-logo brandkit-logo-collapsed';
                sidebarBrand.prepend(collapsedImg);
            }

            const sideW = bData.sidebar_logo_width || 145;
            const sideH = bData.sidebar_logo_height || 40;
            const sideShape = bData.sidebar_logo_shape || bData.logo_shape || 'standard';
            const sideBgPill = Number(bData.sidebar_logo_bg_pill) === 1
                ? 'background: rgba(255,255,255,0.94); padding: 5px 10px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.16);'
                : '';

            const collSize = bData.collapsed_logo_size || 38;
            const collShape = bData.collapsed_logo_shape || 'rounded';
            const fit = bData.logo_fit || 'contain';

            function syncCollapsedState() {
                const isCollapsed = document.body.classList.contains('navbar-collapsed') || document.body.classList.contains('menu-collapsed');
                expandedImg.style.display = isCollapsed ? 'none' : 'block';
                collapsedImg.style.display = isCollapsed ? 'block' : 'none';
            }

            resolveImageWithFallback(expandedCandidates, function (validExpandedUrl) {
                expandedImg.src = validExpandedUrl;
                expandedImg.alt = bData.page_title_brand || 'Menu Principal';
                expandedImg.style.cssText = `max-width: ${sideW}px; max-height: ${sideH}px; width: auto; height: auto; object-fit: ${fit}; margin: 0 auto; transition: all 0.2s ease; ${getShapeInlineStyle(sideShape, primaryColor, true)} ${sideBgPill}`;
                syncCollapsedState();
            });

            resolveImageWithFallback(collapsedCandidates, function (validCollapsedUrl) {
                collapsedImg.src = validCollapsedUrl;
                collapsedImg.alt = bData.page_title_brand || 'Menu Compacto';
                collapsedImg.style.cssText = `width: ${collSize}px; height: ${collSize}px; max-width: ${collSize}px; max-height: ${collSize}px; object-fit: ${fit}; margin: 0 auto; transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1); ${getShapeInlineStyle(collShape, primaryColor, true)}`;
                syncCollapsedState();
            });

            const legacySpan = sidebarBrand.querySelector('.glpi-logo');
            if (legacySpan) {
                legacySpan.style.display = 'none';
            }

            // Observador reativo para o botão "Recolher menu" (.reduce-menu -> body.navbar-collapsed)
            const bodyObserver = new MutationObserver(syncCollapsedState);
            bodyObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });

            const reduceBtn = document.querySelector('.reduce-menu');
            if (reduceBtn) {
                reduceBtn.addEventListener('click', function () {
                    requestAnimationFrame(syncCollapsedState);
                    setTimeout(syncCollapsedState, 60);
                });
            }
        }

        // 4b. Layout Horizontal / Interface Simplificada (Helpdesk Topbar)
        if (topbarBrand && topbarBrand !== sidebarBrand) {
            resolveImageWithFallback(expandedCandidates, function (validTopUrl) {
                let topImg = topbarBrand.querySelector('.brandkit-logo-expanded');
                if (!topImg) {
                    topImg = document.createElement('img');
                    topImg.className = 'brandkit-sidebar-logo brandkit-logo-expanded';
                    topbarBrand.prepend(topImg);
                }

                const sideW = bData.sidebar_logo_width || 145;
                const sideH = bData.sidebar_logo_height || 40;
                const sideShape = bData.sidebar_logo_shape || bData.logo_shape || 'standard';
                const fit = bData.logo_fit || 'contain';

                topImg.src = validTopUrl;
                topImg.alt = bData.page_title_brand || 'Início';
                topImg.style.cssText = `display: block; max-width: ${sideW}px; max-height: ${sideH}px; width: auto; height: auto; object-fit: ${fit}; ${getShapeInlineStyle(sideShape, primaryColor, true)}`;

                const legacyTopSpan = topbarBrand.querySelector('.glpi-logo');
                if (legacyTopSpan) {
                    legacyTopSpan.style.display = 'none';
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBrandkitIcons);
    } else {
        initBrandkitIcons();
    }
})();
