# ADR-0007 — Ciclo de vida de conta isolado em service

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O plugin `MultipleLocalAuth` possui regras complexas de autenticação: troca
forçada de senha, recuperação de conta na lixeira, regras de senha e bloqueio.

## Decisão

Isolar as regras de ciclo de vida da conta no
`MultipleLocalAuth/AccountLifecycleService.php`, tornando-as testáveis sem
depender diretamente do container `App`.

## Consequências

- Melhora a testabilidade das regras de autenticação.
- Centraliza a lógica de troca forçada e restauração de lixeira.
- Mantém o `Plugin.php` e o `Provider.php` mais enxutos.
