> Criado em: 2026-08-21 · Última revisão: 2026-08-21
> Regra: doc desatualizado é corrigido ou marcado como obsoleto — nunca deixado apodrecendo em silêncio.

---
name: exemplo-skill
description: Use este arquivo como referência de formato ao criar novas skills. Não é carregada automaticamente em produção.
---

# Exemplo de skill

## Pré-requisitos

- Branch atualizada com `master`/`develop`.
- Ambiente de desenvolvimento Docker em execução.

## Procedimento

1. Copie este arquivo para `.agents/skills/<nome-da-skill>/SKILL.md`.
2. Substitua o `name`, `description`, título e passos.
3. Certifique-se de que a `description` diz QUANDO a skill deve ser usada.
4. Submeta para aprovação explícita antes de usá-la.

## Critérios de pronto

- [ ] Testes passando (<!-- TODO: preencher comando de teste -->)
- [ ] Tipos checados (<!-- TODO: preencher comando de typecheck -->)
- [ ] Documentação atualizada se o contrato mudou
