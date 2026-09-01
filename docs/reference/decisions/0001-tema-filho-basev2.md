# ADR-0001 — Tema filho da BaseV2

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O Mapas Culturais fornece a interface moderna na BaseV2. O Pará precisava de
uma identidade visual própria sem reescrever toda a interface.

## Decisão

Criar o tema `MapasPA` como filho de
`MapasCulturais\Themes\BaseV2\Theme` (`themes/MapasPA/Theme.php:8`).
A customização fica em overrides pontuais: paleta de cores, logo, favicons,
manifesto PWA, injeção do Tawk.to e componente `entity-location`.

## Consequências

- Reduz duplicação de código de UI.
- Amarra a evolução do front à BaseV2.
- Qualquer mudança significativa na BaseV2 pode exigir ajustes no tema.
