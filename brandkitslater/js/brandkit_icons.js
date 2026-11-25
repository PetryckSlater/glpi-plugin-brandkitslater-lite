/**
 * -----------------------------------------------------------------------------
 * Plugin BrandKitSlater - Utilitários de Ícones, Favicon, Logotipo e Harmonização de UI
 * -----------------------------------------------------------------------------
 */
(function () {
    'use strict';

    function initBrandkitIcons() {
        const bData = window.BRANDKIT_DATA || {};
        const timestamp = Date.now();

        // 1. Aplicação Dinâmica de Favicon Customizado (Aba do Navegador)
        (function applyFavicon() {
            let faviconUrl = bData.favicon_url || '';
            if (!faviconUrl) {
                faviconUrl = '/plugins/brandkitslater/pics/favicon.ico?v=' + timestamp;
            }

            const img = new Image();
            img.src = faviconUrl;
            img.onload = function () {
                setFaviconTags(faviconUrl);
            };
            img.onerror = function () {
                // Tenta formato PNG como fallback
                const pngFallback = '/plugins/brandkitslater/pics/favicon.png?v=' + timestamp;
                const imgPng = new Image();
                imgPng.src = pngFallback;
                imgPng.onload = function () {
                    setFaviconTags(pngFallback);
                };
            };

            function setFaviconTags(url) {
                let links = document.querySelectorAll("link[rel*='icon']");
                if (links.length > 0) {
                    links.forEach(l => {
                        l.href = url;
                    });
                } else {
                    let link = document.createElement('link');
                    link.type = 'image/x-icon';
                    link.rel = 'shortcut icon';
                    link.href = url;
                    document.head.appendChild(link);
                }
            }
        })();

        // Utilitário para gerar estilo de formato (Shape)
        function getShapeStyle(shape) {
            switch (shape) {
                case 'rounded':
                    return 'border-radius: 12px !important;';
                case 'circle':
                    return 'border-radius: 50% !important; aspect-ratio: 1/1 !important; object-fit: cover !important;';
                case 'pill':
                    return 'border-radius: 9999px !important;';
                case 'bordered':
                    return 'border: 2px solid #003876 !important; padding: 6px !important; background-color: rgba(255, 255, 255, 0.95) !important; border-radius: 14px !important; box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;';
                case 'glow':
                    return 'filter: drop-shadow(0 0 10px rgba(0, 149, 183, 0.65)) !important;';
                default:
                    return '';
            }
        }

        // 2. Harmonização da Tela de Login e Injeção do Logotipo
        const isLoginPage = document.querySelector('body.welcome-anonymous, body.page-anonymous, form[action*="login"]');
        if (isLoginPage) {
            // Título principal do cartão
            const mainHeading = document.querySelector('.card-body .card-header h2, .card-body h2.mx-auto');
            if (mainHeading && (mainHeading.textContent.includes('Faça login') || mainHeading.textContent.includes('login'))) {
                mainHeading.textContent = 'Entrar no Sistema';
            }

            // Subtítulo descritivo profissional
            const richContainer = document.querySelector('.card-body .rich_text_container');
            if (richContainer) {
                richContainer.innerHTML = '<span style="font-size: 0.88rem; color: #64748b; font-weight: 500; display: block; margin-top: -4px; margin-bottom: 1.25rem; text-align: center;">Acesso corporativo seguro</span>';
            }

            // Botão de submissão do formulário
            const submitBtn = document.querySelector('.card-body button[type="submit"]');
            if (submitBtn && submitBtn.textContent.trim().toLowerCase().includes('entrar')) {
                submitBtn.textContent = 'Acessar Atendimento';
            }

            // Injeta Logotipo no Topo do Card de Login com Fallback Seguro
            const loginLogoUrl = bData.logo_url || ('/plugins/brandkitslater/pics/logo.png?v=' + timestamp);

            function renderCardLogo(url) {
                const cardBody = document.querySelector('.card.card-md .card-body, .card .card-body');
                if (!cardBody) return;

                let img = document.querySelector('.brandkit-card-logo');
                if (!img) {
                    img = document.createElement('img');
                    img.className = 'brandkit-card-logo';
                    cardBody.insertBefore(img, cardBody.firstChild);
                }

                const loginW = bData.login_logo_width || 200;
                const loginH = bData.login_logo_height || 55;
                const fit = bData.logo_fit || 'contain';
                const shape = bData.logo_shape || 'standard';

                img.src = url;
                img.alt = 'Logotipo';
                img.style.cssText = `display: block; max-width: ${loginW}px; max-height: ${loginH}px; width: auto; height: auto; margin: 0 auto 1.25rem auto; object-fit: ${fit}; transition: all 0.25s ease; ${getShapeStyle(shape)}`;
            }

            const testLogo = new Image();
            testLogo.onload = function () {
                renderCardLogo(loginLogoUrl);
            };
            testLogo.onerror = function () {
                const svgFallback = '/plugins/brandkitslater/pics/logo.svg?v=' + timestamp;
                const testSvg = new Image();
                testSvg.onload = function () {
                    renderCardLogo(svgFallback);
                };
                testSvg.onerror = function () {
                    const pngFallback = '/plugins/brandkitslater/pics/logo.png?v=' + timestamp;
                    if (loginLogoUrl !== pngFallback) {
                        const testPng = new Image();
                        testPng.onload = function () {
                            renderCardLogo(pngFallback);
                        };
                        testPng.src = pngFallback;
                    }
                };
                testSvg.src = svgFallback;
            };
            testLogo.src = loginLogoUrl;
        }

        // 3. Logotipo da Barra Lateral (Painel Interno do GLPI)
        const sidebarBrand = document.querySelector('.navbar-vertical .navbar-brand, .navbar-brand');
        if (sidebarBrand && !isLoginPage) {
            const sideLogoUrl = bData.logo_url || ('/plugins/brandkitslater/pics/logo.png?v=' + timestamp);

            function renderSideLogo(url) {
                let img = sidebarBrand.querySelector('.brandkit-sidebar-logo');
                if (!img) {
                    img = document.createElement('img');
                    img.className = 'brandkit-sidebar-logo';
                    sidebarBrand.prepend(img);
                }

                const sideW = bData.sidebar_logo_width || 140;
                const sideH = bData.sidebar_logo_height || 36;
                const fit = bData.logo_fit || 'contain';
                const shape = bData.logo_shape || 'standard';

                img.src = url;
                img.alt = 'Logotipo';
                img.style.cssText = `max-width: ${sideW}px; max-height: ${sideH}px; width: auto; height: auto; object-fit: ${fit}; margin-right: 8px; vertical-align: middle; transition: all 0.2s ease; ${getShapeStyle(shape)}`;

                // Oculta o logo antigo/texto GLPI
                const oldGlpi = sidebarBrand.querySelector('.glpi-logo, svg');
                if (oldGlpi) {
                    oldGlpi.style.display = 'none';
                }
            }

            const testSide = new Image();
            testSide.onload = function () {
                renderSideLogo(sideLogoUrl);
            };
            testSide.onerror = function () {
                const svgSide = '/plugins/brandkitslater/pics/logo.svg?v=' + timestamp;
                const testSideSvg = new Image();
                testSideSvg.onload = function () {
                    renderSideLogo(svgSide);
                };
                testSideSvg.src = svgSide;
            };
            testSide.src = sideLogoUrl;
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBrandkitIcons);
    } else {
        initBrandkitIcons();
    }
})();
