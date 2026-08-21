# Product Requirements Document (PRD) — Mapa Cultural do Pará

> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

## Visão

Plataforma Mapas Culturais implantada para o estado do Pará, composta por tema
próprio (`MapasPA`) e plugins específicos, executada via containers Docker.

## Requisitos funcionais (RF)

### RF-01 — Identidade visual do Pará
O tema `MapasPA` deve aplicar a identidade visual do Pará (paleta de cores, logo,
favicons, manifesto PWA) sobre a BaseV2 do Mapas Culturais.

### RF-02 — Home editorial customizada
A home deve exibir banners, título, texto e botão de ação injetados pelo plugin
`SettingsPa`, incluindo campanhas como Paulo Gustavo e cultura/sustentabilidade.

### RF-03 — Cadastro de agentes com segmento cultural
O cadastro de agentes deve permitir a seleção de **segmento cultural**
(taxonomia `segmento`) e deve exibi-lo no perfil do agente e em campos de
inscrição.

### RF-04 — Campos obrigatórios de agente
O cadastro de agentes deve tornar obrigatórios, conforme configuração, campos
como nome, gênero, CPF, raça, e-mail, telefone, estado e município.

### RF-05 — Região de Integração (geoRI) nos formulários e relatórios
O endereço do agente responsável deve refletir a **Região de Integração**
(`geoRI`) como campo somente leitura quando preenchido. A coluna `geoRI` deve
aparecer na tabela de inscrições e nas planilhas exportadas.

### RF-06 — Integração com Metabase
A plataforma deve expor painéis do Metabase para oportunidades, usuários,
entidades, agentes, espaços, eventos e projetos.

### RF-07 — Integração com Analytics
A plataforma deve permitir a instalação/ativação do plugin Analytics para
coleta de métricas de uso.

### RF-08 — Autenticação múltipla
A plataforma deve suportar autenticação local por CPF e OAuth (Google,
Facebook, LinkedIn, Twitter) via plugin `MultipleLocalAuth`.

### RF-09 — Rotas amigáveis para editais LPG
Devem existir rotas amigáveis (`lpg/artes-visuais`, etc.) apontando para
oportunidades específicas do edital Paulo Gustavo.

### RF-10 — Endpoints administrativos
Devem existir endpoints administrativos para estatísticas, remoção de arquivos
vazios, envio de e-mails em massa e reabertura de avaliações (uso interno).

## Requisitos não-funcionais (RNF)

- **RNF-01** — A aplicação deve rodar em containers Docker (Nginx, PHP-FPM,
  Redis, PostGIS, MailHog).
- **RNF-02** — O build de assets do tema e dos plugins deve ocorrer no processo
  de construção da imagem Docker.
- **RNF-03** — A plataforma deve manter acessibilidade conforme plugin
  `Accessibility`.
- **RNF-04** — A personalização de comportamento deve preferencialmente usar
  hooks do Mapas Culturais, evitando forks do core.

## Critérios de aceitação

- O tema `MapasPA` compila com `npm run build` sem erros.
- A home exibe os conteúdos injetados pelo `SettingsPa`.
- O campo segmento aparece no cadastro de agentes e é persistido.
- A coluna `geoRI` é exibida na tabela de inscrições e exportada em planilhas.
- Os painéis do Metabase carregam corretamente.
- As rotas amigáveis dos editais LPG resolvem para as oportunidades corretas.

## Fora do escopo

- Alterações no core do Mapas Culturais (exceto via hooks/plugins).
- Reescrita do tema pai BaseV2.
- Funcionalidades não ativadas nos plugins de submódulos.
