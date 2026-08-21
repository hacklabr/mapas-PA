# ADR-0009 — Extensão da busca por palavra-chave via DQL

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

A busca por palavra-chave padrão do Mapas Culturais não considera campos de
metadados configurados (ex.: município, logradouro).

## Decisão

O plugin `MetadataKeyword` altera o DQL dos repositórios via hooks
`repo(<<*>>).getIdsByKeywordDQL.join` e
`repo(<<*>>).getIdsByKeywordDQL.where`, incluindo os metadados configurados
na busca.

## Consequências

- A busca global passa a considerar endereço e outros metadados.
- Pode impactar a performance da busca conforme a quantidade de metadados.
- A configuração dos metadados afeta diretamente os resultados.
