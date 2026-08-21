# Arquitetura — Mapa Cultural do Pará

> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

## Visão geral

A plataforma é uma instalação do Mapas Culturais (PHP) customizada via:

- **Tema:** `themes/MapasPA/` — front-end e sobreposições de comportamento.
- **Plugins:** `plugins/` — extensões de negócio e integrações.
- **Infraestrutura:** Docker Compose com Nginx, PHP-FPM, Redis, PostGIS e MailHog.

## Componentes

### Tema `MapasPA`
- Local: `themes/MapasPA/`
- Herda de `MapasCulturais\Themes\BaseV2\Theme`.
- Responsável pela identidade visual (cores, logo, favicons, manifesto PWA),
  injeção do script Tawk.to e override do componente `entity-location`.
- Build: Laravel Mix/Webpack via `@mapas/scripts`
  (`themes/MapasPA/package.json`).

### Plugin `SettingsPa`
- Local: `plugins/SettingsPa/`
- Único plugin com código no checkout local.
- Registra hooks em templates, metadados de entidade, campos de inscrição,
  colunas de tabelas/planilhas e rotas administrativas.
- Controller: `plugins/SettingsPa/Controller.php`.
- Taxonomia `segmento`: registrada em `plugins/SettingsPa/Plugin.php`.
- Partes de template customizadas: `plugins/SettingsPa/layouts/parts/HomeContent/`.

### Plugins de submódulos
- Local: `plugins/Accessibility/`, `AdminLoginAsUser/`, `Analytics/`,
  `CreateGeoDivisions/`, `MapasBlame/`, `Metabase/`, `MetadataKeyword/`,
  `MultipleLocalAuth/`, `SpamDetector/`, `ValuersManagement/`.
- Mantidos como submódulos Git (ver `.gitmodules`).
- Configurações ativas em `docker/common/config.d/plugins.php` e
  `docker/production/config.d/authentication.php`.

#### `Accessibility`
- Widget V-Libras, controles de fonte/contraste e ícones customizados.
- Componentes: `accessibility-controls`, `accessibility-controls-itens`.

#### `AdminLoginAsUser`
- Impersonação administrativa via sessão `auth.asUserId`.
- Botões de "logar como" na gestão de usuários e página do agente.

#### `Analytics`
- Inserção de script de analytics na tag `<head>` (BaseV1 e BaseV2).
- Chave configurável via `env('ANALYTICS_KEY')`.

#### `CreateGeoDivisions`
- Importação de divisões geográficas a partir de CSVs em `import-files/`.

#### `MapasBlame`
- Auditoria de requisições e remoção de entidades.
- Controller `blame`, entidade `Blame`, tabelas `blame_request`/`blame_log`.
- Componente: `blame-table`.

#### `Metabase`
- Incorporação de dashboards públicos do Metabase.
- Controller `metabase` com rotas `dashboard` e `panel`.
- Componentes: `home-metabase`, `metabase-dashboard`, `list-dashboard`.

#### `MetadataKeyword`
- Extensão da busca por palavra-chave para campos de metadados configurados.
- Altera DQL dos repositórios via hooks.

#### `MultipleLocalAuth`
- Autenticação local (e-mail/CPF) + social (Google, Facebook, LinkedIn,
  Twitter, Login Cidadão, Gov.br, Decidim).
- Regras de força de senha, reCAPTCHA, bloqueio por tentativas, confirmação de
  e-mail, recuperação de senha, troca forçada e recuperação de conta na
  lixeira.
- Services: `AccountLifecycleService.php`, `GovBr/GovBrAccountService.php`.
- Componentes: `login`, `create-account`, `change-password`,
  `password-strongness`.

#### `SpamDetector`
- Monitoramento de criação/edição de entidades por termos suspeitos.
- Notificação de administradores e movimentação para lixeira
  (`status = -10`).
- Componentes: `spam-add-config`, `spam-warning`.

#### `ValuersManagement`
- Distribuição de avaliadores em lote via planilha Excel.
- Atualiza `registration.valuers` e `valuers_exceptions_list`.
- Componente: `evalmaster-upload`.

### Infraestrutura
- Nginx (`docker/nginx.conf`).
- PHP-FPM com imagem base `hacklab/mapasculturais:7.8.6`
  (`docker/Dockerfile`).
- Redis para cache e sessões.
- PostGIS (`postgis/postgis:14-master`) para dados geoespaciais.
- MailHog para captura de e-mails em desenvolvimento.

### Configuração da aplicação
- Arquivos PHP em `docker/common/config.d/` e `docker/production/config.d/`.
- Definem tema ativo, plugins habilitados, textos, rotas, LGPD, autenticação e
  hierarquia de divisões geográficas.

## Decisões técnicas vigentes

Ver `docs/reference/decisions/`.
