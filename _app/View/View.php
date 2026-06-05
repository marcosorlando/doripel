<?php

namespace App\View;

use function array_keys;
use function array_map;
use function array_values;
use function explode;
use function file_get_contents;
use function get_defined_constants;
use function implode;
use function is_object;
use function is_scalar;
use function method_exists;
use function sprintf;
use function str_replace;

/**
 * View.class [ HELPER MVC ]
 * Reponsável por carregar o template, povoar e exibir a view, povoar e incluir arquivos PHP no sistem.
 * Arquitetura MVC!
 * @copyright (c) 2025, Marcos Orlando - ZEN AGÊNCIA WEB
 */
class View
{
    /** @var array<string,mixed> */
    private array $data = [];

    /** @var string[] */
    private array $keys = [];

    /** @var array<int, string> */
    private array $values = [];

    private ?string $template = null;

    public function __construct(private readonly string $path) { }

    /**
     * <b>Carregar template View:</b> Dentro da pasta do seu template, crie a pasta _tpl e armazene as
     * <b>template_views</b>.tpl.html. Depois basta informar APENAS O NOME do arquivo para carregar o mesmo!
     *
     * @param string $template Nome do arquivo da view
     */
    public function load(string $template): string
    {

        $content = file_get_contents(sprintf('%s/%s.tpl.html', $this->path, $template));
        $this->template = false === $content ? '' : $content;

        return $this->template;
    }

    /**
     * <b>Exibir template View:</b> Execute um foreach com um getResult() do seu model e informe o envelope
     * neste método para configurar a view. Não esqueça de carregar a view acima do foreach com o método Load.
     *
     * @param array<string,mixed> $data Array com dados obtidos
     * @param string $view Template carregado pelo método load()
     */
    public function show(array $data, string $view): void
    {

        $this->setKeys($data);
        $this->setValues();
        $this->showView($view);
    }

    // Executa o tratamento dos campos para substituição de chaves na view.

    /**
     * @param array<string,mixed> $data
     */
    private function setKeys(array $data): void
    {

        $this->data = $data;
        $this->keys = explode('&', '{' . implode('}&{', array_keys($this->data)) . '}');
    }

    // Obtém os valores a serem inseridos nas chaves da view.
    private function setValues(): void
    {

        $this->values = array_map(
            static fn(mixed $value): string => self::normalizeValue($value),
            array_values($this->data)
        );
    }

    // Exibe o template view com echo!

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

    private function showView(string $view): void
    {

        $this->template = $view;
        $userConstants = get_defined_constants(true)['user'] ?? [];
        $userConstantsStringMap = [];
        foreach ($userConstants as $constantName => $constantValue) {
            if ('' === $constantName) {
                continue;
            }

            $userConstantsStringMap[$constantName] = self::normalizeValue($constantValue);
        }
        $this->template = str_replace(
            array_keys($userConstantsStringMap),
            array_values($userConstantsStringMap),
            $this->template
        );
        echo str_replace($this->keys, $this->values, $this->template);
    }
}
