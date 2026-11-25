# 🎨 BrandKitSlater Lite — Customizador Visual & Theming para GLPI 10 & 11

![GLPI 10](https://img.shields.io/badge/GLPI-10.0.x-blue.svg?style=for-the-badge&logo=php)
![GLPI 11](https://img.shields.io/badge/GLPI-11.0.x-success.svg?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-GPL--2.0-orange.svg?style=for-the-badge)
![Edition](https://img.shields.io/badge/Edition-Community%20Lite-purple.svg?style=for-the-badge)

**BrandKitSlater Lite** é uma suíte visual moderna e open-source para **GLPI 10 e GLPI 11**, desenvolvida para transformar a identidade visual da sua Central de Serviços e Service Desk sem a necessidade de editar arquivos internos do core do GLPI ou quebrar durante atualizações de versão.

---

## ✨ Recursos da Versão Community Lite

### 🖼️ Tela de Login Glassmorphism & Split-Screen
- **Layout Moderno:** Cartão flutuante em Glassmorphism com desfoque de fundo (`backdrop-filter: blur(12px)`), bordas translúcidas e sombras suaves.
- **Fundo Personalizado (Wallpaper):** Defina a imagem de fundo via URL ou faça upload direto de arquivos JPG, PNG ou WebP pelo painel.
- **Gradiente Dinâmico de Contraste:** Slider ajustável de 0% a 95% para escurecer o fundo sob medida e garantir legibilidade total no formulário.
- **Atalho de Auto-Cadastro:** Ativação/desativação rápida de botão para portais externos ou criação de contas públicas.

### 🎨 Paleta de Cores e Modo Híbrido
- **Paletas Rápidas de 1 Clique:**
  - 🏢 **Corporate Slate:** Tons neutros corporativos de alta tecnologia com acentos índigo.
  - ☀️ **Clean Light:** Visual minimalista claro e arejado com toques em azul royal.
  - 🌿 **Emerald Nature:** Tons verdes e terra com forte apelo ecológico e sustentável.
  - 🍷 **Bordeaux:** Elegância clássica em bordô e dourado suave.
- **Modo Híbrido Inteligente:**
  - Permite escolher entre manter o painel interno no *Light Mode padrão do GLPI* aplicando o tema apenas no login, OU estender o brandkit completo para o cabeçalho, barra lateral e botões do sistema.

### 🛡️ Gestão de Logotipo & Favicon
- **Logotipo Customizado:** Suporte a SVG, PNG e WebP com preview instantâneo e controles de alinhamento e proporção.
- **Favicon Dinâmico da Aba:** Upload e atualização automática do ícone da aba do navegador em múltiplos formatos (`.ico`, `.png`, `.svg`).

### 🖥️ Studio de Preview Interativo em Tempo Real
- Visualize as mudanças antes de salvar! O painel administrativo conta com abas simuladas de:
  1. **Tela de Login** (com inputs e botões em miniatura).
  2. **Painel Interno GLPI** (com simulação da sidebar, navbar e cartões de chamados).
  3. **Aba do Navegador & Favicon** (com visualização do ícone em escalas de 16px, 32px e 48px).

---

## 📸 Demonstração da Arquitetura

```text
GLPI Core
   │
   ├── [Hook: Display Central Header / Add Header]
   │       └── Injeção assíncrona do CSS gerado dinamicamente
   │
   └── BrandKitSlater Lite
           ├── Painel de Configuração (Studio em tempo real)
           ├── Engine de Compilação de CSS (Variáveis e Glassmorphism)
           └── Armazenamento Seguro no MySQL/MariaDB (Tabela glpi_plugin_brandkitslater_configs)
```

---

## 📦 Instalação

### Opção 1: Via Git Clone
Acesse a pasta de plugins do seu servidor GLPI:
```bash
cd /var/www/html/glpi/plugins
git clone https://github.com/PetryckSlater/glpi-plugin-brandkitslater-lite.git brandkitslater
chown -R www-data:www-data brandkitslater
```

### Opção 2: Download do Release
1. Baixe o pacote `.zip` da versão mais recente na aba [Releases](../../releases).
2. Extraia o conteúdo na pasta `plugins/` garantindo que o diretório se chame estritamente **`brandkitslater`**:
   ```text
   glpi/
   └── plugins/
       └── brandkitslater/
           ├── setup.php
           ├── hook.php
           ├── css/
           ├── front/
           ├── inc/
           ├── js/
           └── pics/
   ```
3. Abra o seu GLPI como Administrador e acesse **Configurar > Plugins**.
4. Localize **BrandKitSlater (Community Lite)**, clique em **Instalar** e em seguida em **Ativar**.
5. Acesse **Configurar > BrandKitSlater Studio** no menu para personalizar o tema!

---

## 🛠️ Requisitos do Sistema

- **GLPI:** Versões 10.0.0 até 11.0.x
- **PHP:** 8.1 ou superior (com extensão `pdo_mysql` habilitada)
- **Navegadores Homologados:** Google Chrome, Mozilla Firefox, Microsoft Edge, Safari e Opera

---

## 📄 Licença

Distribuído sob a licença **GPL-2.0**. Consulte o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Autor & Portfólio

Desenvolvido por **Petryck Slater**  
- Especialista em Infraestrutura de TI, DevOps & Customizações GLPI.
- LinkedIn: [linkedin.com/in/petryck-slater](https://www.linkedin.com)
- GitHub: [github.com/PetryckSlater](https://github.com/PetryckSlater)
