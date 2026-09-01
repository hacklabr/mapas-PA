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

### RF-11 — Acessibilidade
A plataforma deve oferecer controles de acessibilidade: V-Libras, ajuste de
tamanho de fonte e alternância de contraste.

### RF-12 — Impersonação administrativa
Administradores devem poder logar como outro usuário para suporte, com indicação
visual no header e capacidade de retorno ao perfil original.

### RF-13 — Auditoria de requisições
A plataforma deve registrar logs de requisições (método, rota, IP, navegador,
SO, dispositivo) e ações de remoção de entidades, com interface de consulta.

### RF-14 — Autenticação social e Gov.br
Além da autenticação local por e-mail/CPF, devem ser suportados provedores
Google, Facebook, LinkedIn, Twitter, Login Cidadão, Gov.br e Decidim.

### RF-15 — Regras de segurança de autenticação
A autenticação local deve impor força de senha, reCAPTCHA, bloqueio por
tentativas, confirmação de e-mail, recuperação de senha e troca forçada de
senha.

### RF-16 — Detecção e moderação de spam
A plataforma deve monitorar criação/edição de entidades por termos suspeitos,
notificar administradores e mover conteúdo/usuário para lixeira quando termos
estiverem na lista de bloqueio.

### RF-17 — Gestão de avaliadores em lote
Gestores de edital devem poder distribuir avaliadores por comissão via upload
de planilha Excel, com histórico de processamentos.

### RF-18 — Busca por metadados
A busca por palavra-chave deve considerar campos de metadados configurados
(ex.: município, logradouro).

### RF-19 — Importação de divisões geográficas
Deve ser possível importar divisões geográficas customizadas a partir de CSVs.

### RF-20 — Analytics
A plataforma deve inserir script de analytics na tag `<head>` com chave
configurável via ambiente.

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
- Login por Gov.br, Google e CPF funcionam sem erros.
- Controles de acessibilidade (V-Libras, contraste, fonte) são exibidos e funcionam.
- Logs de auditoria são registrados e consultáveis.
- Distribuição de avaliadores por planilha atualiza as inscrições corretamente.
- Testes de `MultipleLocalAuth` passam (`composer install && vendor/bin/phpunit`).

## Fora do escopo

- Alterações no core do Mapas Culturais (exceto via hooks/plugins).
- Reescrita do tema pai BaseV2.
- Funcionalidades não ativadas nos plugins de submódulos.
