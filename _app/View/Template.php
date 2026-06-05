<?php

namespace App\View;

use function file_get_contents;
use function is_file;
use function is_object;
use function is_scalar;
use function method_exists;
use function str_replace;

/**
 * Check.class [ HELPER ]
 * Classe responável por manipular e validar dados do sistema!
 * @copyright (c) 2025, Marcos Orlando - ZEN AGÊNCIA WEB
 */
class Template
{
    private static string $Html = '';

    public static function getTemplate(string $template, string $folder): string
    {

        $path = $folder . $template;

        if (is_file($path)) {
            $content = file_get_contents($path);
            self::$Html = false === $content ? '' : $content;
        } else {
            self::$Html = '';
        }

        return self::$Html;
    }

    /**
     * @param array<string, mixed> $arrayData
     */
    public static function setTemplate(string $template, array $arrayData): string
    {

        self::$Html = $template;

        foreach ($arrayData as $key => $value) {
            if ('' === $key) {
                continue;
            }

            $normalized = self::normalizeValue($value);
            self::$Html = str_replace('{' . $key . '}', $normalized, self::$Html);
        }

        return self::$Html;
    }

    private static function normalizeValue(mixed $value): string
    {

        if (null === $value) {
            return '';
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string)$value;
        }

        return '';
    }
}
