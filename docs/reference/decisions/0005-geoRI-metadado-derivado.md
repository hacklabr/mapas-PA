# ADR-0005 — Região de Integração (geoRI) como metadado derivado

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O governo do Pará organiza seu território em Regiões de Integração. A
plataforma precisa vincular agentes e inscrições a essa divisão territorial
para relatórios e formulários.

## Decisão

Tratar `geoRI` como um metadado derivado do endereço do agente. O campo é
exibido como somente leitura no formulário de endereço
(`themes/MapasPA/components/entity-location/template.php`) e adicionado como
colina na tabela de inscrições e nas planilhas exportadas por hooks em
`plugins/SettingsPa/Plugin.php`
(`component(opportunity-registrations-table).additionalHeaders` e
`SpreadsheetJob(registrations-spreadsheets).getHeader:after`).

## Consequências

- Conceito transversal entre formulários e relatórios.
- Depende da correta configuração da hierarquia geográfica
  (`app.geoDivisionsHierarchy`).
- Alterações na lógica de cálculo da RI exigem ajustes no plugin.
