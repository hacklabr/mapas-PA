# Jornadas de usuário — Mapa Cultural do Pará

> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

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

### J08 — Usuário acessa com autenticação social ou Gov.br
O usuário escolhe o provedor de login (Google, Gov.br, Decidim, etc.), autoriza
o acesso e, quando necessário, fornece um e-mail alternativo para resolver
conflitos de conta.

### J09 — Usuário recupera conta excluída
Ao fazer login com senha correta em uma conta na lixeira, o usuário recebe um
e-mail de confirmação e, após confirmar, a conta e as entidades relacionadas
são restauradas.

### J10 — Administrador atua como outro usuário
O administrador usa a função "logar como" para acessar o painel como outro
usuário, executa ações de suporte e retorna ao seu perfil original.

### J11 — Visitante ajusta acessibilidade
O visitante ativa V-Libras, aumenta/diminui a fonte ou alterna o contraste do
site.

### J12 — Administrador modera spam
O administrador recebe alerta de conteúdo suspeito, revisa termos detectados,
marcar o conteúdo como "não spam" ou move usuário/conteúdo para a lixeira.

### J13 — Gestor distribui avaliadores por planilha
O gestor de edital envia planilha Excel com comissões de avaliadores (modo
complementar ou substituir), processa o arquivo e acompanha o histórico.

### J14 — Operador importa divisões geográficas
O operador carrega CSV de municípios ou Regiões de Integração no plugin
`CreateGeoDivisions` para atualizar a base geográfica.
