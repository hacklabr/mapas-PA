# ADR-0004 — Domínio cultural do Pará modelado como taxonomia segmento

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O Pará precisava classificar agentes culturais por segmento (artes visuais,
música, etc.) e exibir essa classificação no perfil e em inscrições.

## Decisão

Modelar o segmento cultural como uma taxonomia fixa registrada no plugin
`SettingsPa` (`plugins/SettingsPa/Plugin.php:231-271`). A lista de termos fica
no código, com opção de remoção do termo "Outros" via variável de ambiente
`REMOVE_OTHER_SEGMENT`.

## Consequências

- Classificação unificada em todo o sistema.
- Alterar a lista de segmentos exige deploy.
- O termo "Outros" pode ser removido sem alterar o código principal.
