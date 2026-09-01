# ADR-0006 — Gov.br: identidade por CPF e e-mail único

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O plugin `MultipleLocalAuth` suporta login via Gov.br. O Gov.br pode fornecer
um e-mail que já está vinculado a outra conta local, criando risco de *account
hijack* ou duplicidade.

## Decisão

O matching de conta Gov.br ocorre apenas por `sub`/`cpf`
(`MultipleLocalAuth/GovBr/GovBrAccountService.php`). Quando o e-mail do Gov.br
já existe em outra conta, o fluxo é interrompido e a UI coleta um e-mail
alternativo (`auth/govbr-email`) antes de criar ou vincular a conta.

## Consequências

- Evita que um usuário assuma indevidamente uma conta existente.
- Adiciona uma etapa extra de coleta de e-mail quando há conflito.
- O CPF torna-se o identificador canônico para contas Gov.br.
