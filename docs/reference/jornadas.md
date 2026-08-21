# Jornadas de usuário — Mapa Cultural do Pará

> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

<!-- TODO: preencher com as jornadas dos principais atores (gestor cultural, agente cultural, avaliador, administrador) após análise do código legado (STAGE 4). -->

## Atores

- Visitante / cidadão
- Agente cultural (individual ou coletivo)
- Inscrito em edital
- Gestor publicador de edital
- Avaliador
- Administrador / saasAdmin
- Gestor de dados / BI

## Jornadas

### J01 — Visitante acessa a home
O visitante abre a plataforma, vê conteúdo editorial (banners, título, texto),
pode usar o chat Tawk.to e navega pelas entidades culturais.

### J02 — Agente cultural se cadastra
O agente preenche o cadastro com dados pessoais, seleciona o segmento cultural,
preenche endereço (CEP, logradouro, estado, município) e define se o endereço é
público. A Região de Integração (`geoRI`) é exibida quando disponível.

### J03 — Inscrito participa de edital
O inscrito vincula seu agente à inscrição, preenche campos obrigatórios (incluindo
`@terms:segmento` quando aplicável) e acompanha o status da inscrição.

### J04 — Gestor publica e gerencia edital
O gestor cria/gerencia oportunidades, visualiza inscrições na tabela (com coluna
`geoRI`), exporta planilhas e acompanha painéis do Metabase.

### J05 — Avaliador analisa inscrições
O avaliador acessa as inscrições atribuídas e emite pareceres. Em casos
excepcionais, um administrador pode reabrir permissões de avaliação.

### J06 — Administrador executa rotinas internas
O administrador acessa endpoints administrativos para estatísticas, correção de
arquivos vazios, envio de comunicações em massa e outras operações de suporte.

### J07 — Gestor de dados consulta BI
O gestor de dados visualiza painéis do Metabase sobre oportunidades, usuários,
entidades, agentes, espaços, eventos e projetos.
