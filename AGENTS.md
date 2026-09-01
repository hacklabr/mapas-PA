> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

# AGENTS.md — Mapa Cultural do Pará

## 1. Contexto do projeto

Instalação do Mapas Culturais para o estado do Pará. O código customizado vive
no tema `MapasPA` (front-end via Laravel Mix/Webpack) e em plugins específicos
no diretório `plugins/`. A aplicação roda em containers Docker (PHP-FPM,
Nginx, Redis, PostGIS).

A fonte de verdade do produto é o PRD vivo em `docs/reference/prd.md`.

## 2. Comandos verificáveis

| Ação | Comando |
|---|---|
| Build geral (Docker) | `docker-compose up --build` |
| Build do tema | `cd themes/MapasPA && npm run build` |
| Build de um plugin | `cd plugins/<NOME_DO_PLUGIN> && npm run build` |
| Watch do tema | `cd themes/MapasPA && npm run watch` |
| Testes | `cd plugins/MultipleLocalAuth && composer install && ./vendor/bin/phpunit` |
| Lint | <!-- TODO: preencher (não detectado no inventário) --> |
| Typecheck | <!-- TODO: preencher (não detectado no inventário) --> |

Rode os comandos relevantes antes de declarar qualquer tarefa pronta.

## 3. Mapa da estrutura

- `themes/MapasPA/` — tema principal do Pará; assets compilados via
  `package.json` usando `@mapas/scripts`.
- `plugins/` — plugins customizados do projeto (ex.: `Analytics`, `Metabase`,
  `SettingsPa`, `ValuersManagement`).
- `docker/` — configurações de imagem e configurações do container.
- `docker-compose.yml` — orquestração local/produção (Nginx, app, db, Redis,
  MailHog).
- `dev-scripts/` — scripts auxiliares de desenvolvimento ( quando existirem ).
- `docs/reference/` — documentação viva do produto e da arquitetura.

## 4. Regras invioláveis

- Nunca commitar sem rodar os testes.
- Nunca criar arquivos sem necessidade.
- Nunca editar migrations já aplicadas.
- Nunca adicionar dependências sem justificar.
- Nunca desativar checks de CI para fazer o build passar.

## 5. Convenções

As convenções vivem em `docs/reference/conventions/` (`code-style.md`,
`git-workflow.md`, `api-design.md`). Leia antes de escrever código — este
arquivo aponta, não duplica.

## 6. Workflow esperado

- Planeje antes de codar.
- Rode os testes antes de declarar pronto.
- Formato de commit e PR/MR conforme `docs/reference/conventions/git-workflow.md`.
- Consulte `docs/reference/jornadas.md` antes de alterar fluxos de usuário.

## 7. Ponteiros

- `docs/reference/prd.md` → produto e requisitos (fonte de verdade)
- `docs/reference/jornadas.md` → fluxos de usuário
- `docs/reference/arquitetura/INDEX.md` → fonte de verdade da arquitetura
  (índice roteador — carregue cada doc só quando relevante)
- `docs/reference/decisions/` → ADRs (registros de decisão técnica)
- `.agents/skills/` → catálogo de procedimentos sob demanda

## Skills — procedimentos sob demanda

Regras sempre ativas ficam neste arquivo; procedimentos vivem em
`.agents/skills/`. Um procedimento só vira skill quando é repetível,
multi-etapa ou de alto custo de erro — e não-óbvio (se qualquer agente acerta
sem orientação, não precisa de skill).

**Evolução contínua:** quando uma decisão consolidada ou padrão recorrente
emergir no dia a dia (ex.: arquitetura de módulos definida, convenção de
widgets estabilizada), proponha uma skill usando
`.agents/skills/exemplo-skill/SKILL.md` como formato — nunca crie sem
aprovação explícita.

## ADRs são imutáveis

Decisão nova = ADR novo em `docs/reference/decisions/` (sequência de 4
dígitos a partir do máximo existente), que referencia o substituído. Nunca
edite um ADR aceito; nunca renumere ADRs existentes. Formato:
`docs/reference/decisions/0000-template-adr.md`.
