# 🎨 BrandKitSlater Lite v2.2 — Customizador Visual Nativo & Theming Studio para GLPI 10 & 11

![GLPI 10](https://img.shields.io/badge/GLPI-10.0.x-blue.svg?style=for-the-badge&logo=php)
![GLPI 11](https://img.shields.io/badge/GLPI-11.0.x-success.svg?style=for-the-badge&logo=php)
![Version](https://img.shields.io/badge/Version-2.2.0--lite-0ea5e9.svg?style=for-the-badge)
![License](https://img.shields.io/badge/License-GPL--2.0-orange.svg?style=for-the-badge)

**BrandKitSlater Lite** é um estúdio open-source completo de identidade visual para **GLPI 10 e GLPI 11**, projetado para integrar-se nativamente à arquitetura Tabler/Bootstrap do GLPI sem modificar nenhum arquivo do core.

---

## 🚀 Novidades da Versão 2.2 (Deep GLPI Integration)

### 🧭 1. Controle Independente: Menu Inteiro vs. Menu Recolhido (70px)
- **Logo do Menu Inteiro (Expandido):** Configure um logotipo horizontal exclusivo para a barra lateral aberta (com opção de fundo claro de contraste e versões claras para sidebars escuras).
- **Ícone do Menu Recolhido (`70px` Rail):** Configure um ícone compacto `1:1` exclusivo para quando o usuário clica em **`« Recolher menu`** (`body.navbar-collapsed`), evitando logos espremidos ou cortados!
- **Troca Reativa Instantânea (Zero-FOUC):** Sincronização dupla via CSS Container Queries (`@container`) + `MutationObserver` no DOM e variáveis nativas `--glpi-logo` / `--glpi-logo-reduced`.
- **Formatos Independentes:** Escolha estilos exclusivos para o ícone recolhido (`Badge Glass Translúcido`, `Arredondado`, `Circular`, `Com Borda` ou `Glow`).

### 🎨 2. Integração Profunda com Tokens Nativos do GLPI 11
- Mapeamento direto nas variáveis oficiais `--glpi-mainmenu-bg`, `--glpi-mainmenu-fg`, `--tblr-primary`, `--tblr-primary-rgb` e `--tblr-border-radius`.
- **Submenus Flutuantes (Flyouts):** Harmonização completa dos menus suspensos quando a barra lateral está recolhida em `70px`, preservando a área de hover sem gaps.
- **Controles de Ergonomia:** Ajuste a largura da barra lateral aberta (`200px–300px`), o design dos links (`Pílula SaaS`, `Nativo GLPI` ou `Glassmorphism`), o raio global de bordas (`0–20px`) e a elevação dos cartões internos.
- **Atalho Nativo no Menu:** Acesso direto em **Configurar > BrandKit Studio** na barra lateral do GLPI.

### 🚪 3. Tela de Login Glassmorphism 2.0 & Suporte Total a 2FA/MFA
- Cartão em vidro fosco (`backdrop-filter: blur(24px) saturate(190%)`) com bordas especulares e animação suave respeitando `prefers-reduced-motion`.
- **Textos 100% Editáveis:** Personalize o selo superior, título, subtítulo, botão de acesso, marca na aba do navegador (`<title>`) e botão opcional de auto-cadastro externo.
- Estilização nativa homologada para as telas de **TOTP / 2FA / Códigos de Backup** do GLPI 11.

### 🖥️ 4. Live Studio Interativo em Tempo Real
- **Simulador de Menu Recolhível ao Vivo:** Clique em `📐 Testar Menu Recolhido (70px)` ou diretamente em `« Recolher menu` dentro do preview para validar a transição entre o Logo Inteiro e o Ícone Compacto antes de salvar!
- **Multi-Viewport & Acessibilidade:** Alterne entre Desktop, Tablet e Mobile com medidor dinâmico de contraste **WCAG AA / AAA** em tempo real.

---

## 📦 Instalação

### Opção 1: Via Git Clone
```bash
cd /var/www/html/glpi/plugins
git clone https://github.com/PetryckSlater/glpi-plugin-brandkitslater-lite.git brandkitslater_repo
mv brandkitslater_repo/brandkitslater ./brandkitslater
rm -rf brandkitslater_repo
chown -R www-data:www-data brandkitslater
```

### Opção 2: Pacote `.zip` Pronto
1. Baixe `brandkitslater-lite-v2.2.0.zip` (ou na aba [Releases](../../releases)).
2. Extraia em `glpi/plugins/` garantindo que a pasta se chame **`brandkitslater`**.
3. Acesse **Configurar > Plug-ins**, clique em **Instalar** e **Ativar**.
4. Abra **Configurar > BrandKit Studio** no menu lateral e personalize!

---

## 📄 Licença & Autor

Distribuído sob a licença **GPL-2.0**. Veja [LICENSE](LICENSE).  
Desenvolvido por **Petryck Slater** — [github.com/PetryckSlater](https://github.com/PetryckSlater)
