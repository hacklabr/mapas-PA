# ADR-0003 — Configuração da aplicação por arquivos em config.d

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

O Mapas Culturais permite customizar comportamentos via arquivos PHP em
diretórios de configuração, sem modificar o core.

## Decisão

Toda a personalização do Pará (tema ativo, plugins habilitados, textos, rotas,
LGPD, autenticação, hierarquia de divisões geográficas) é feita por arquivos
PHP em `docker/common/config.d/` e `docker/production/config.d/`, montados nos
containers via volumes.

## Consequências

- Configuração versionada junto ao deploy.
- Comportamento de negócio concentrado em arquivos de configuração.
- Mudanças em configurações exigem rebuild/redeploy dos containers.
