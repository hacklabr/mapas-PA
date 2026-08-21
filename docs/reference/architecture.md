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
