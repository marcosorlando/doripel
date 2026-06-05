#!/usr/bin/env bash
set -euo pipefail

MODE=""
TARGET_DIR=""
REPORT_FILE=""

usage() {
  cat <<EOF
Uso:
  $0 --dry-run <diretorio>
  $0 --apply <diretorio> [--report <arquivo>]

Exemplos:
  $0 --dry-run admin/_sis
  $0 --apply admin/_sis --report reports/php-modernizer.txt
EOF
}

log() {
  local msg="$1"
  echo "$msg"
  if [[ -n "${REPORT_FILE:-}" ]]; then
    echo "$msg" >> "$REPORT_FILE"
  fi
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
          echo "Argumento invalido: $1"
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
    echo "Diretorio nao encontrado: $TARGET_DIR"
    exit 1
  fi

  if [[ -n "$REPORT_FILE" ]]; then
    mkdir -p "$(dirname "$REPORT_FILE")"
    : > "$REPORT_FILE"
  fi
}

transform_file() {
  local file="$1"
  local out="$2"

  # Normaliza duplicidade de ponto e virgula em encerramentos legados (ex.: endforeach;;)
  perl -0pi -e 's/\b(endif|endforeach|endfor|endwhile|endswitch)\s*;;/$1;/gs' "$out"

  perl -0pi -e 's/\bif\s*\((.*?)\)\s*:/if ($1) {/gs' "$out"
  perl -0pi -e 's/\belseif\s*\((.*?)\)\s*:/} elseif ($1) {/gs' "$out"
  perl -0pi -e 's/\belse\s*:/} else {/gs' "$out"
  perl -0pi -e 's/\bendif\s*;/}/gs' "$out"

  perl -0pi -e 's/\bforeach\s*\((.*?)\)\s*:/foreach ($1) {/gs' "$out"
  perl -0pi -e 's/\bendforeach\s*;/}/gs' "$out"

  perl -0pi -e 's/\bfor\s*\((.*?)\)\s*:/for ($1) {/gs' "$out"
  perl -0pi -e 's/\bendfor\s*;/}/gs' "$out"

  perl -0pi -e 's/\bwhile\s*\((.*?)\)\s*:/while ($1) {/gs' "$out"
  perl -0pi -e 's/\bendwhile\s*;/}/gs' "$out"

  perl -0pi -e 's/\bswitch\s*\((.*?)\)\s*:/switch ($1) {/gs' "$out"
  perl -0pi -e 's/\bendswitch\s*;/}/gs' "$out"

  if ! diff -q "$file" "$out" >/dev/null 2>&1; then
    if [[ "$MODE" == "dry-run" ]]; then
      log "[DRY-RUN] Mudancas detectadas: $file"
      diff -u "$file" "$out" || true
    else
      cp "$out" "$file"
      log "[APPLY] Alterado: $file"
    fi
  fi
}

run_lint() {
  log "Rodando lint..."
  local lint_out
  lint_out="$(mktemp)"
  while IFS= read -r -d '' file; do
    if ! php -l "$file" > "$lint_out" 2>&1; then
      log "[LINT-ERROR] $file"
      cat "$lint_out"
      if [[ -n "${REPORT_FILE:-}" ]]; then
        cat "$lint_out" >> "$REPORT_FILE"
      fi
    elif [[ -n "${REPORT_FILE:-}" ]]; then
      cat "$lint_out" >> "$REPORT_FILE"
    fi
  done < <(find "$TARGET_DIR" \
    -type d \( -name vendor -o -name .git \) -prune -o \
    -type f -name "*.php" -print0)
  rm -f "$lint_out"
}

main() {
  parse_args "$@"

  log "========================================"
  log "PHP Legacy Generic Modernizer"
  log "Modo: $MODE"
  log "Diretorio: $TARGET_DIR"
  log "========================================"

  while IFS= read -r -d '' file; do
    tmp="$(mktemp)"
    cp "$file" "$tmp"
    transform_file "$file" "$tmp"
    rm -f "$tmp"
  done < <(find "$TARGET_DIR" \
    -type d \( -name vendor -o -name .git \) -prune -o \
    -type f -name "*.php" -print0)

  run_lint
  log "Concluido."
}

main "$@"
