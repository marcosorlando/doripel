# Manual Prático de Uso — WorkControl Legacy Fixer

## Objetivo
Este manual explica como usar o Codex como IA principal para análise, revisão, decisão e apoio à execução de correções em módulos PHP legados do WorkControl.

---

## 1. Visão geral

Neste projeto, o Codex pode usar estes recursos:

- `copilot-instructions.md`
  - contexto global do projeto

- `.github/instructions/*.instructions.md`
  - regras automáticas para arquivos PHP

- `.github/agents/*.agent.md`
  - especialistas para tarefas específicas

- `.github/skills/workcontrol-legacy-fixer/SKILL.md`
  - workflow reutilizável focado no WorkControl

- `.github/prompts/*.prompt.md`
  - prompts reutilizáveis para tarefas recorrentes

---

## 2. Sim, você consegue chamar isso pelo Codex

### Resposta curta
Sim. Você consegue usar essas customizações com o Codex como sua principal IA de codificação.

### Importante
O jeito mais confiável é citar explicitamente no prompt:

- o nome da skill
- ou o nome do agent
- ou o nome do prompt

### Exemplo ideal
Quanto mais explícito você for, maior a chance do Codex usar corretamente o contexto certo.
```text
Use a skill workcontrol-legacy-fixer para revisar o diretório admin/_siswc/products
