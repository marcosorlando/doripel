<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;

use function class_exists;
use function htmlspecialchars;
use function is_int;
use function is_string;
use function strtotime;
use function time;

final class DateHelper
{
    /**
     * Data para atributo HTML datetime (YYYY-MM-DD) no fuso especificado.
     */
    public static function iso(mixed $value, string $tz = 'America/Sao_Paulo'): string
    {

        $ts = self::toTimestamp($value);

        return (new DateTimeImmutable("@{$ts}"))
            ->setTimezone(new DateTimeZone($tz))
            ->format('Y-m-d');
    }

    /**
     * Converte string|int|DateTimeInterface|null em timestamp seguro.
     */
    public static function toTimestamp(mixed $value): int
    {

        if ($value instanceof DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && '' !== $value) {
            $ts = strtotime($value);
            if (false !== $ts) {
                return $ts;
            }
        }

        return time();
    }

    /**
     * Data/hora em RFC3339 (bom para APIs, logs, microdata).
     */
    public static function rfc3339(mixed $value, string $tz = 'America/Sao_Paulo'): string
    {

        $ts = self::toTimestamp($value);

        return (new DateTimeImmutable("@{$ts}"))
            ->setTimezone(new DateTimeZone($tz))
            ->format(DATE_RFC3339);
    }

    /**
     * Data legível em PT-BR via IntlDateFormatter.
     * Padrão: "14 de setembro de 2025".
     * Se Intl não estiver disponível, fallback: d/m/Y.
     */
    public static function human(
        mixed $value,
        string $pattern = "dd 'de' MMMM 'de' yyyy",
        string $tz = 'America/Sao_Paulo',
        string $locale = 'pt_BR'
    ): string {

        $ts = self::toTimestamp($value);

        if (class_exists(IntlDateFormatter::class)) {
            $fmt = new IntlDateFormatter(
                $locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                $tz,
                null,
                $pattern
            );
            $out = $fmt->format($ts);
            if (false !== $out) {
                return $out; // já em UTF-8
            }
        }

        // Fallback simples se Intl indisponível
        return (new DateTimeImmutable("@{$ts}"))
            ->setTimezone(new DateTimeZone($tz))
            ->format('d/m/Y');
    }

    /**
     * Atalho comum: dd/MM/yyyy HH:mm (24h).
     */
    public static function humanWithTime(
        mixed $value,
        string $tz = 'America/Sao_Paulo'
    ): string {

        // Se quiser com nomes PT-BR, use pattern ICU: "dd/MM/yyyy HH:mm" na human()
        $ts = self::toTimestamp($value);

        return (new DateTimeImmutable("@{$ts}"))
            ->setTimezone(new DateTimeZone($tz))
            ->format('d/m/Y H:i');
    }

    /**
     * Normaliza string "YYYY-mm-dd HH:ii:ss" (MySQL) para timestamp.
     */
    public static function fromMysql(string $mysqlDateTime): int
    {

        $ts = strtotime($mysqlDateTime);

        return false !== $ts ? $ts : time();
    }

    /**
     * Para escapar valores em HTML (atalho local).
     * Use sempre que imprimir saída do helper dentro de atributos/tags.
     */
    public static function e(string $s): string
    {

        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
