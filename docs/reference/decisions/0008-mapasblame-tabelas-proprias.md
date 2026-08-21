# ADR-0008 — Auditoria de requisições em tabelas próprias

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

A plataforma precisa auditar requisições e ações de remoção de entidades para
fins de segurança e rastreabilidade.

## Decisão

Criar o plugin `MapasBlame` com tabelas próprias (`blame_request`,
`blame_log`), entidade `Blame` e controller `blame`. O plugin escuta o hook
global `mapasculturais.run:before` para registrar método, rota, IP,
navegador, SO e dispositivo.

## Consequências

- Auditoria centralizada e consultável via interface.
- Gera volume de dados que pode crescer rapidamente.
- Levanta questões de privacidade e retenção de logs.
