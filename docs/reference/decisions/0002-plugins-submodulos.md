# ADR-0002 — Plugins mantidos como submódulos Git

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O projeto utiliza vários plugins desenvolvidos pela comunidade ou reutilizados
entre instalações do Mapas Culturais (Accessibility, Analytics, Metabase,
MultipleLocalAuth, etc.).

## Decisão

Mant esses plugins como submódulos Git (`.gitmodules`), apontando para
repositórios externos. O plugin específico do Pará (`SettingsPa`) fica no
próprio repositório.

## Consequências

- Facilita atualizações isoladas de cada plugin.
- Torna o build dependente da disponibilidade e do checkout dos submódulos.
- O checkout local pode ficar sem o código dos submódulos se não forem
  inicializados.
