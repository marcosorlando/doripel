<?php

declare(strict_types=1);

namespace App\Bootstrap;

use ErrorException;
use ReflectionClass;
use Throwable;

use function error_get_last;
use function error_reporting;
use function header;
use function headers_sent;
use function htmlspecialchars;
use function http_response_code;
use function in_array;
use function defined;
use function is_numeric;
use function json_encode;
use function strtolower;
use function trim;
use function ob_end_clean;
use function ob_get_level;
use function register_shutdown_function;
use function set_exception_handler;
use function sprintf;

final class ErrorHandler
{
    public static function register(bool $asJson = false): void
    {

        set_error_handler(
            static function (int $severity, string $message, ?string $file = null, ?int $line = null): bool {

                if ((error_reporting() & $severity) === 0) {
                    return false;
                }

                throw new ErrorException($message, 0, $severity, $file ?? 'unknown', $line ?? 0);
            }
        );

        set_exception_handler(static function (Throwable $e) use ($asJson): void {

            http_response_code(500);
            if ($asJson) {
                if (!headers_sent()) {
                    header('Content-Type: application/json; charset=UTF-8');
                }

                $payload = [
                    'error' => true,
                    'message' => 'Erro interno',
                    'type' => (new ReflectionClass($e))->getShortName(),
                    'code' => $e->getCode(),
                ];

                if (self::isDebugEnabled()) {
                    $payload['exception_message'] = $e->getMessage();
                    $payload['file'] = $e->getFile();
                    $payload['line'] = $e->getLine();
                    $payload['trace'] = $e->getTrace();
                }

                echo json_encode($payload, JSON_UNESCAPED_UNICODE);
            } else {
                self::clearOutputBuffers();
                if (!headers_sent()) {
                    header('Content-Type: text/html; charset=UTF-8');
                }
                echo self::renderHtmlError($e);
            }
        });

        register_shutdown_function(static function (): void {

            $err = error_get_last();
            if (
                null !== $err && in_array(
                    $err['type'],
                    [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR],
                    true
                )
            ) {
                http_response_code(500);
                // Em produção: logue; em dev: mostre
            }
        });
    }

    private static function clearOutputBuffers(): void
    {

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private static function renderHtmlError(Throwable $e): string
    {

        $type = htmlspecialchars((new ReflectionClass($e))->getShortName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $line = (string)$e->getLine();
        $trace = htmlspecialchars($e->getTraceAsString(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $code = (string)$e->getCode();
        $column = self::extractColumnFromThrowable($e);
        $columnLabel = null !== $column ? (string)$column : 'n/d';

        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Erro Interno</title>
<style>
    .wc-error-wrap, .wc-error-wrap * { box-sizing: border-box !important; }
    .wc-error-wrap {
        --bg: #050812;
        --panel: #0a1222;
        --panel-2: #0d172c;
        --text: #ffffff;
        --muted: #dbe3ef;
        --danger: #ff7b7b;
        --warning: #ffd166;
        --border: #3b4c6b;
        --link: #9dd6ff;
        --link-hover: #d9efff;
        font: 15px/1.6 "Inter", "Segoe UI", Roboto, Arial, sans-serif !important;
        color: var(--text) !important;
        min-height: 100vh;
        margin: 0;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(1200px 700px at -10% -10%, #1f2a44 0%, transparent 60%),
            radial-gradient(900px 600px at 110% 20%, #1d3557 0%, transparent 55%),
            var(--bg);
    }

    .wc-error-wrap a { color: var(--link) !important; text-decoration: underline !important; }
    .wc-error-wrap a:hover { color: var(--link-hover) !important; }
    .wc-error-shell {
        width: min(1120px, 100%);
        border: 1px solid var(--border);
        border-radius: 14px;
        background: linear-gradient(180deg, var(--panel), var(--panel-2));
        box-shadow: 0 20px 55px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    .wc-error-head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        background: rgba(0, 0, 0, 0.18);
    }
    .wc-error-dot { width: 10px; height: 10px; border-radius: 50%; }
    .wc-error-dot.red { background: #ff5f57; }
    .wc-error-dot.yellow { background: #febc2e; }
    .wc-error-dot.green { background: #28c840; }
    .wc-error-content { padding: 20px; }
    .wc-error-badge {
        display: inline-block;
        margin-bottom: 10px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255, 107, 107, 0.6);
        background: rgba(127, 29, 29, 0.45);
        color: #ffe4e6;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
    }
    .wc-error-title {
        margin: 0 0 6px;
        font-size: clamp(28px, 3vw, 38px);
        line-height: 1.1;
        color: #ffffff;
    }
    .wc-error-msg {
        margin: 0 0 16px;
        font-size: 20px;
        color: #f1f5f9;
        font-weight: 500;
    }
    .wc-error-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        margin-bottom: 14px;
    }
    .wc-error-meta {
        padding: 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: rgba(2, 6, 23, .55);
    }
    .wc-error-k {
        display: block;
        margin-bottom: 4px;
        color: #e2e8f0;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
    }
    .wc-error-v {
        color: #ffffff;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: 14px;
        word-break: break-word;
    }
    .wc-error-meta-file .wc-error-v {
        white-space: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        word-break: normal;
        display: block;
        scrollbar-width: thin;
    }
    .wc-error-grid-compact {
        grid-template-columns: repeat(3, minmax(150px, 1fr));
    }
    .wc-error-help {
        margin: 14px 0;
        padding: 12px;
        border: 1px solid rgba(251, 191, 36, .6);
        border-radius: 10px;
        background: rgba(120, 53, 15, .35);
        color: #fef3c7;
    }
    .wc-error-help strong { color: #fde68a; }
    .wc-error-help ul { margin: 8px 0 0 18px; padding: 0; }
    .wc-error-help li { margin: 4px 0; }
    .wc-error-trace {
        margin-top: 14px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: rgba(2, 6, 23, .75);
        overflow: hidden;
    }
    .wc-error-trace summary {
        padding: 12px 14px;
        cursor: pointer;
        color: #ffffff;
        font-weight: 700;
        border-bottom: 1px solid var(--border);
        list-style: none;
    }
    .wc-error-trace summary::-webkit-details-marker { display: none; }
    .wc-error-trace pre {
        margin: 0;
        padding: 14px;
        max-height: 48vh;
        overflow: auto;
        color: #f8fafc;
        font-size: 13px;
        line-height: 1.5;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>
<body>
<section class="wc-error-wrap">
    <article class="wc-error-shell">
        <header class="wc-error-head">
            <span class="wc-error-dot red"></span>
            <span class="wc-error-dot yellow"></span>
            <span class="wc-error-dot green"></span>
        </header>
        <div class="wc-error-content">
            <span class="wc-error-badge">Erro interno detectado</span>
            <h1 class="wc-error-title">{$type}</h1>
            <p class="wc-error-msg">{$message}</p>

            <div class="wc-error-meta wc-error-meta-file">
                <span class="wc-error-k">Arquivo</span>
                <span class="wc-error-v" title="{$file}">{$file}</span>
            </div>

            <div class="wc-error-grid wc-error-grid-compact">
                <div class="wc-error-meta"><span class="wc-error-k">Linha</span><span class="wc-error-v">{$line}</span></div>
                <div class="wc-error-meta"><span class="wc-error-k">Coluna</span><span class="wc-error-v">{$columnLabel}</span></div>
                <div class="wc-error-meta"><span class="wc-error-k">Código</span><span class="wc-error-v">{$code}</span></div>
            </div>

            <aside class="wc-error-help">
                <strong>Como interpretar este erro (PT-BR)</strong>
                <ul>
                    <li><strong>Tipo:</strong> categoria técnica do erro/exceção.</li>
                    <li><strong>Mensagem:</strong> o problema principal detectado pelo PHP.</li>
                    <li><strong>Arquivo/Linha:</strong> local exato onde a falha aconteceu.</li>
                    <li><strong>Código:</strong> identificador numérico da exceção (quando houver).</li>
                    <li><strong>Stack trace:</strong> sequência de chamadas até chegar ao erro.</li>
                </ul>
            </aside>

            <details class="wc-error-trace" open>
                <summary>Stack trace completo</summary>
                <pre>{$trace}</pre>
            </details>
        </div>
    </article>
</section>
</body>
</html>
HTML;
    }

    private static function isDebugEnabled(): bool
    {

        if (defined('APP_DEBUG')) {
            $value = APP_DEBUG;
            if (is_numeric($value)) {
                return (int)$value === 1;
            }

            $normalized = strtolower(trim((string)$value));

            return in_array($normalized, ['1', 'true', 'on', 'yes'], true);
        }

        return false;
    }

    private static function extractColumnFromThrowable(Throwable $e): ?int
    {

        $message = $e->getMessage();
        if (\preg_match('/(?:coluna|column)\s+(\d+)/iu', $message, $m) === 1) {
            return (int)$m[1];
        }

        if (\preg_match('/line\s+\d+:(\d+)/i', $message, $m) === 1) {
            return (int)$m[1];
        }

        $trace = $e->getTraceAsString();
        if (\preg_match('/^#0 .*?\((\d+):(\d+)\):/m', $trace, $m) === 1) {
            return (int)$m[2];
        }

        return null;
    }
}
