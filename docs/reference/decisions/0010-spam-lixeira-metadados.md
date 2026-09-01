# ADR-0010 — Moderação de spam por lixeira e metadados

**Status:** aceito  
**Data:** 2026-08-21  
**Round:** setup

## Contexto

A plataforma precisa detectar e moderar conteúdo suspeito em Agentes,
Oportunidades, Projetos, Espaços e Eventos.

## Decisão

O plugin `SpamDetector` monitora criação/edição dessas entidades por termos
suspeitos. Termos na lista de bloqueio movem a entidade e o usuário para
lixeira (`status = -10`). Termos suspeitos geram notificação para
administradores (limitada a uma a cada 24h). Administradores podem marcar
conteúdo como "não spam" (`spam_status = 2`).

## Consequências

- Moderação semi-automática de conteúdo.
- Risco de falsos positivos movendo conteúdo legítimo para lixeira.
- Dependência da manutenção da lista de termos.
