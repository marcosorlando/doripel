#!/usr/bin/env bash
set -euo pipefail

MODE=""
TARGET_DIR=""
REPORT_FILE=""
BACKUP=true

usage() {
  cat <<EOF
Uso:
  $0 --dry-run <diretorio>
  $0 --apply <diretorio> [--no-backup] [--report <arquivo>]

Exemplos:
  $0 --dry-run admin/_siswc/products
  $0 --apply admin/_siswc/products
  $0 --apply admin/_siswc/products --report reports/workcontrol-fix-report.txt
  $0 --apply admin/_siswc/products --no-backup

Modos:
  --dry-run    Mostra o que seria feito sem alterar arquivos
  --apply      Aplica as alterações nos arquivos

Opções:
  --no-backup  Não cria arquivos .bak
  --report     Salva relatório em arquivo
EOF
}

log() {
  local msg="$1"
  echo "$msg"
  if [[ -n "${REPORT_FILE:-}" ]]; then
    echo "$msg" >> "$REPORT_FILE"
  fi
}

backup_file() {
  local file="$1"
  if [[ "$BACKUP" == true ]]; then
    cp "$file" "$file.bak"
  fi
}

process_file_dry_run() {
  local file="$1"
  local tmp
  tmp="$(mktemp)"
  cp "$file" "$tmp"

  perl -0pi -e 's/\bif\s*\((.*?)\)\s*:/if ($1) {/gs' "$tmp"
  perl -0pi -e 's/\belseif\s*\((.*?)\)\s*:/} elseif ($1) {/gs' "$tmp"
  perl -0pi -e 's/\belse\s*:/} else {/gs' "$tmp"
  perl -0pi -e 's/\bendif\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bforeach\s*\((.*?)\)\s*:/foreach ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendforeach\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bfor\s*\((.*?)\)\s*:/for ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendfor\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bwhile\s*\((.*?)\)\s*:/while ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendwhile\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bswitch\s*\((.*?)\)\s*:/switch ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendswitch\s*;/}/gs' "$tmp"

  perl -0pi -e 's/(?<!::)(?<!->)\bErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/(?<!::)(?<!->)\bAjaxErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/(?<!::)(?<!->)\bajaxErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/->ExeRead\s*\(/->exeRead(/g' "$tmp"
  perl -0pi -e 's/->FullRead\s*\(/->fullRead(/g' "$tmp"
  perl -0pi -e 's/->ExeCreate\s*\(/->exeCreate(/g' "$tmp"
  perl -0pi -e 's/->ExeUpdate\s*\(/->exeUpdate(/g' "$tmp"
  perl -0pi -e 's/->ExeDelete\s*\(/->exeDelete(/g' "$tmp"
  perl -0pi -e 's/->ExePager\s*\(/->exePager(/g' "$tmp"
  perl -0pi -e 's/->ExePaginator\s*\(/->exePaginator(/g' "$tmp"
  perl -0pi -e 's/->ReturnPage\s*\(/->returnPage(/g' "$tmp"
  perl -0pi -e 's/\$Read\s*=\s*new\s+Read\s*;/\$Read ??= new Read();/g' "$tmp"
  perl -0pi -e 's/\$Create\s*=\s*new\s+Create\s*;/\$Create ??= new Create();/g' "$tmp"
  perl -0pi -e 's/\$Update\s*=\s*new\s+Update\s*;/\$Update ??= new Update();/g' "$tmp"
  perl -0pi -e 's/\$Delete\s*=\s*new\s+Delete\s*;/\$Delete ??= new Delete();/g' "$tmp"
  perl -0pi -e 's/\$Upload\s*=\s*new\s+Upload\s*;/\$Upload ??= new Upload();/g' "$tmp"
  perl -0pi -e 's/\$Read\s*=\s*new\s+Read\s*\(\s*\)\s*;/\$Read ??= new Read();/g' "$tmp"
  perl -0pi -e 's/\$Create\s*=\s*new\s+Create\s*\(\s*\)\s*;/\$Create ??= new Create();/g' "$tmp"
  perl -0pi -e 's/\$Update\s*=\s*new\s+Update\s*\(\s*\)\s*;/\$Update ??= new Update();/g' "$tmp"
  perl -0pi -e 's/\$Delete\s*=\s*new\s+Delete\s*\(\s*\)\s*;/\$Delete ??= new Delete();/g' "$tmp"
  perl -0pi -e 's/\$Upload\s*=\s*new\s+Upload\s*\(\s*\)\s*;/\$Upload ??= new Upload();/g' "$tmp"
  perl -0pi -e 's/\bgetWcLevel\s*\(/Check::getWcLevel(/g' "$tmp"
  perl -0pi -e 's/\bgetCapilalize\s*\(/Check::getCapilalize(/g' "$tmp"
  perl -0pi -e 's/\bdadedo\s*\(/Check::getCapilalize(/g' "$tmp"
  perl -0pi -e "s/array_map\(\s*'htmlspecialchars'\s*,\s*([^)]+)\)/array_map(static fn(\\\$value) => Check::safeHtmlChars(\\\$value), \$1)/g" "$tmp"
  perl -0pi -e 's/\bhtmlspecialchars\s*\(\s*([^()]+?)\s*\)/Check::safeHtmlChars($1)/g' "$tmp"
  perl -0pi -e "s#require\s+['\"]\.\./\.\./_app/Config\.inc\.php['\"];#require __DIR__ . '/../../vendor/autoload.php';#g" "$tmp"

  if ! diff -q "$file" "$tmp" >/dev/null 2>&1; then
    log "[DRY-RUN] Mudanças detectadas: $file"
    diff -u "$file" "$tmp" || true
  fi

  rm -f "$tmp"
}

process_file_apply() {
  local file="$1"
  local tmp
  tmp="$(mktemp)"
  cp "$file" "$tmp"

  perl -0pi -e 's/\bif\s*\((.*?)\)\s*:/if ($1) {/gs' "$tmp"
  perl -0pi -e 's/\belseif\s*\((.*?)\)\s*:/} elseif ($1) {/gs' "$tmp"
  perl -0pi -e 's/\belse\s*:/} else {/gs' "$tmp"
  perl -0pi -e 's/\bendif\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bforeach\s*\((.*?)\)\s*:/foreach ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendforeach\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bfor\s*\((.*?)\)\s*:/for ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendfor\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bwhile\s*\((.*?)\)\s*:/while ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendwhile\s*;/}/gs' "$tmp"

  perl -0pi -e 's/\bswitch\s*\((.*?)\)\s*:/switch ($1) {/gs' "$tmp"
  perl -0pi -e 's/\bendswitch\s*;/}/gs' "$tmp"

  perl -0pi -e 's/(?<!::)(?<!->)\bErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/(?<!::)(?<!->)\bAjaxErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/(?<!::)(?<!->)\bajaxErro\s*\(/Check::ajaxErro(/g' "$tmp"
  perl -0pi -e 's/->ExeRead\s*\(/->exeRead(/g' "$tmp"
  perl -0pi -e 's/->FullRead\s*\(/->fullRead(/g' "$tmp"
  perl -0pi -e 's/->ExeCreate\s*\(/->exeCreate(/g' "$tmp"
  perl -0pi -e 's/->ExeUpdate\s*\(/->exeUpdate(/g' "$tmp"
  perl -0pi -e 's/->ExeDelete\s*\(/->exeDelete(/g' "$tmp"
  perl -0pi -e 's/->ExePager\s*\(/->exePager(/g' "$tmp"
  perl -0pi -e 's/->ExePaginator\s*\(/->exePaginator(/g' "$tmp"
  perl -0pi -e 's/->ReturnPage\s*\(/->returnPage(/g' "$tmp"
  perl -0pi -e 's/\$Read\s*=\s*new\s+Read\s*;/\$Read ??= new Read();/g' "$tmp"
  perl -0pi -e 's/\$Create\s*=\s*new\s+Create\s*;/\$Create ??= new Create();/g' "$tmp"
  perl -0pi -e 's/\$Update\s*=\s*new\s+Update\s*;/\$Update ??= new Update();/g' "$tmp"
  perl -0pi -e 's/\$Delete\s*=\s*new\s+Delete\s*;/\$Delete ??= new Delete();/g' "$tmp"
  perl -0pi -e 's/\$Upload\s*=\s*new\s+Upload\s*;/\$Upload ??= new Upload();/g' "$tmp"
  perl -0pi -e 's/\$Read\s*=\s*new\s+Read\s*\(\s*\)\s*;/\$Read ??= new Read();/g' "$tmp"
  perl -0pi -e 's/\$Create\s*=\s*new\s+Create\s*\(\s*\)\s*;/\$Create ??= new Create();/g' "$tmp"
  perl -0pi -e 's/\$Update\s*=\s*new\s+Update\s*\(\s*\)\s*;/\$Update ??= new Update();/g' "$tmp"
  perl -0pi -e 's/\$Delete\s*=\s*new\s+Delete\s*\(\s*\)\s*;/\$Delete ??= new Delete();/g' "$tmp"
  perl -0pi -e 's/\$Upload\s*=\s*new\s+Upload\s*\(\s*\)\s*;/\$Upload ??= new Upload();/g' "$tmp"
  perl -0pi -e 's/\bgetWcLevel\s*\(/Check::getWcLevel(/g' "$tmp"
  perl -0pi -e 's/\bgetCapilalize\s*\(/Check::getCapilalize(/g' "$tmp"
  perl -0pi -e 's/\bdadedo\s*\(/Check::getCapilalize(/g' "$tmp"
  perl -0pi -e "s/array_map\(\s*'htmlspecialchars'\s*,\s*([^)]+)\)/array_map(static fn(\\\$value) => Check::safeHtmlChars(\\\$value), \$1)/g" "$tmp"
  perl -0pi -e 's/\bhtmlspecialchars\s*\(\s*([^()]+?)\s*\)/Check::safeHtmlChars($1)/g' "$tmp"
  perl -0pi -e "s#require\s+['\"]\.\./\.\./_app/Config\.inc\.php['\"];#require __DIR__ . '/../../vendor/autoload.php';#g" "$tmp"

  if ! diff -q "$file" "$tmp" >/dev/null 2>&1; then
    backup_file "$file"
    cp "$tmp" "$file"
    log "[APPLY] Alterado: $file"
  fi

  rm -f "$tmp"
}

run_lint() {
  log ""
  log "========== LINT PHP =========="
  while IFS= read -r -d '' file; do
    if ! php -l "$file" >> "${REPORT_FILE:-/dev/stdout}" 2>&1; then
      log "[LINT-ERROR] $file"
    fi
  done < <(find "$TARGET_DIR" \
    -type d \( -name vendor -o -name .git \) -prune -o \
    -type f -name "*.php" -print0)
}

parse_args() {
  while [[ $# -gt 0 ]]; do
    case "$1" in
      --dry-run)
        MODE="dry-run"
        shift
        ;;
      --apply)
        MODE="apply"
        shift
        ;;
      --no-backup)
        BACKUP=false
        shift
        ;;
      --report)
        REPORT_FILE="${2:-}"
        shift 2
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        if [[ -z "$TARGET_DIR" ]]; then
          TARGET_DIR="$1"
        else
          echo "Argumento inválido: $1"
          usage
          exit 1
        fi
        shift
        ;;
    esac
  done

  if [[ -z "$MODE" || -z "$TARGET_DIR" ]]; then
    usage
    exit 1
  fi

  if [[ ! -d "$TARGET_DIR" ]]; then
    echo "Diretório não encontrado: $TARGET_DIR"
    exit 1
  fi

  if [[ -n "$REPORT_FILE" ]]; then
    mkdir -p "$(dirname "$REPORT_FILE")"
    : > "$REPORT_FILE"
  fi
}

main() {
  parse_args "$@"

  log "========================================"
  log "WorkControl Legacy Fixer Script"
  log "Modo: $MODE"
  log "Diretório: $TARGET_DIR"
  log "Backup: $BACKUP"
  log "========================================"
  log ""

  while IFS= read -r -d '' file; do
    case "$MODE" in
      dry-run)
        process_file_dry_run "$file"
        ;;
      apply)
        process_file_apply "$file"
        ;;
    esac
  done < <(find "$TARGET_DIR" \
    -type d \( -name vendor -o -name .git \) -prune -o \
    -type f -name "*.php" -print0)

  run_lint

  log ""
  log "Processo concluído."
  log "Revisar manualmente arquivos dinâmicos, imports e semântica de negócio."
}

main "$@"
