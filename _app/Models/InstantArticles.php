<?php

namespace App\Models;

use function array_filter;
use function array_keys;
use function array_values;
use function count;
use function date;
use function is_array;
use function is_numeric;
use function is_string;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strtotime;
use function trim;

/**
 * InstantArticles.class [ HELPER ]
 * Versão 1.0.
 * @copyright (c) 2016, Whallysson Avelino - (whallyssonallain@gmail.com)
 */
class InstantArticles
{
    private ?string $Head = null;

    private ?string $Body = null;

    private ?string $Foot = null;

    private ?string $Slider = null;

    private ?string $Content = null;

    private ?string $Cods = null;

    /** PARAMETERS */
    private ?string $Canonical = null;

    private ?string $Title = null;

    private ?string $Author = null;

    private ?string $Descricao = null;

    private ?string $Capa = null;

    private ?string $DataPub = null;

    private ?string $DataMod = null;

    private ?string $Conteudo = null;

    /** @var null|list<string> */
    private ?array $Slides = null;

    /** @var null|list<string> */
    private ?array $ArticleRel = null;

    private ?string $CodAnalytics = null;

    private ?string $SiteName = null;

    /** REPLACE */
    /** @var array<string,string> */
    private array $ReplaceTag = [
        'pre' => 'p',
        '<br>' => '',
    ];

    /**
     * @param array<string, mixed> $Content
     */
    public function artCreate(array $Content): string
    {

        // Inicia os parametros
        $this->parameters($Content);

        // Head
        $this->Body = $this->header();

        // Analytics
        $this->Body .= $this->analytics();

        // Header
        $this->Body .= '<header>';
        $this->Body .= '<figure>';
        if (null !== $this->Capa && '' !== $this->Capa) {
            $this->Body .= sprintf("<img src='%s' />", $this->Capa);
        }

        $this->Body .= '</figure>';

        if (null !== $this->Title) {
            $this->Body .= sprintf('<h1>%s</h1>', $this->Title);
        }

        if (null !== $this->Descricao) {
            $this->Body .= sprintf('<h2>%s</h2>', $this->Descricao);
        }

        // Autor do artigo
        if (null !== $this->Author) {
            $this->Body .= sprintf('<address>%s</address>', $this->Author);
        }

        $this->Body .= $this->buildTimeTag($this->DataPub, 'op-published');
        $this->Body .= $this->buildTimeTag($this->DataMod, 'op-modified');

        $this->Body .= '</header>';

        // Corpo do artigo
        if (null !== $this->Conteudo) {
            $this->Body .= $this->replaces($this->Conteudo);
        }

        // Slides
        $this->Body .= $this->slide();

        // Footer
        $this->Body .= $this->footer();

        return sprintf('<![CDATA[%s]]>', $this->Body);
    }

    // PRIVATE METHODS

    /**
     * @param array<string, mixed> $Parameters
     */
    private function parameters(array $Parameters): void
    {

        // Inicia/Limpa
        $this->clean();

        $this->Canonical = $this->stringOrNull($Parameters['canonical'] ?? null);
        $this->Title = $this->stringOrNull($Parameters['title'] ?? null);
        $this->Author = $this->stringOrNull($Parameters['author'] ?? null);
        $this->Descricao = $this->stringOrNull($Parameters['desc'] ?? null);
        $this->Capa = $this->stringOrNull($Parameters['capa'] ?? null);

        $this->DataPub = $this->stringOrNull($Parameters['data_pub'] ?? null);
        $this->DataMod = $this->stringOrNull($Parameters['data_mod'] ?? null);
        $this->Conteudo = $this->stringOrNull($Parameters['conteudo'] ?? null);
        $this->CodAnalytics = $this->stringOrNull($Parameters['analytics'] ?? null);

        $slides = $Parameters['slides'] ?? null;
        $this->Slides = is_array($slides) && [] !== $slides ? array_values(
            array_filter($slides, fn($slide) => is_string($slide) && '' !== $slide)
        ) : null;

        $related = $Parameters['artigos_rel'] ?? null;
        $this->ArticleRel = is_array($related) && [] !== $related ? array_values(
            array_filter($related, fn($item) => is_string($item) && '' !== $item)
        ) : null;

        $this->SiteName = $this->stringOrNull($Parameters['site_name'] ?? null);
    }

    private function clean(): void
    {

        $this->Head = null;
        $this->Body = null;
        $this->Foot = null;
        $this->Slider = null;
        $this->Content = null;
        $this->Cods = null;
        $this->Title = null;
        $this->Author = null;
        $this->Descricao = null;
        $this->Capa = null;
        $this->Canonical = null;
        $this->DataPub = null;
        $this->DataMod = null;
        $this->Conteudo = null;
        $this->Slides = null;
        $this->ArticleRel = null;
        $this->CodAnalytics = null;
        $this->SiteName = null;
    }

    private function stringOrNull(mixed $value): ?string
    {

        if (null === $value) {
            return null;
        }

        if (is_string($value) || is_numeric($value)) {
            $normalized = trim((string)$value);

            return '' === $normalized ? null : $normalized;
        }

        return null;
    }

    private function header(): string
    {

        $this->Head = '<!doctype html>';
        $this->Head .= '<html lang="pt-br" prefix="op:http://media.facebook.com/op#">';
        $this->Head .= '<head>';
        $this->Head .= '<meta charset="utf-8">';
        if (null !== $this->Canonical) {
            $this->Head .= sprintf("<link rel='canonical' href='%s' />", $this->Canonical);
        }

        if (null !== $this->Title) {
            $this->Head .= sprintf('<title>%s</title>', $this->Title);
            $this->Head .= sprintf("<meta property='og:title' content='%s' />", $this->Title);
        }

        if (null !== $this->Descricao) {
            $this->Head .= sprintf("<meta property='og:description' content='%s' />", $this->Descricao);
        }

        if (null !== $this->Capa) {
            $this->Head .= sprintf("<meta property='og:image' content='%s' />", $this->Capa);
        }

        $this->Head .= "<meta property='op:markup_version' content='v1.0' />";
        $this->Head .= "<meta property='fb:use_automatic_ad_placement' content='true' />";
        $this->Head .= '</head>';
        $this->Head .= '<body>';
        $this->Head .= '<article>';

        return $this->Head;
    }

    private function analytics(): string
    {

        $this->Cods = null;
        $analytics = null !== $this->CodAnalytics ? trim($this->CodAnalytics) : '';
        if ('' !== $analytics) {
            $sanitized = str_replace(['<script>', '</script>'], '', $analytics);
            $sanitized = str_replace(['     ', '    ', '   ', '  '], ' ', trim($sanitized));
            if ('' !== $sanitized) {
                $this->Cods = '<figure class="op-tracker">';
                $this->Cods .= '<iframe>';
                $this->Cods .= str_replace(["\r\n", "\r", "\n"], '', $sanitized);
                $this->Cods .= '</iframe>';
                $this->Cods .= '</figure>';
            }
        }

        return $this->Cods ?? '';
    }

    private function buildTimeTag(?string $date, string $class): string
    {

        if (null === $date) {
            return '';
        }

        $timestamp = strtotime($date);
        if (false === $timestamp) {
            return '';
        }

        $iso8601 = date('D, d M Y H:i:s O', $timestamp);
        $humanReadable = date('d/m/Y H\hi', $timestamp);

        return sprintf("<time class='%s' dateTime='%s'>%s</time>", $class, $iso8601, $humanReadable);
    }

    // Replaces

    private function replaces(string $Content): string
    {

        $this->Content = $Content;

        // imagem
        $this->Content = $this->pregReplace(
            '/<img.*src=[\'"](.*?)[\'"].*>/i',
            '<figure><img src="$1" /></figure>',
            $this->Content
        );

        // Video (iframe)
        $this->Content = $this->pregReplace(
            '/<iframe.*?src=[\'"](.*?)[\'"].*?<\/iframe>/si',
            '<figure class="op-interactive"><iframe class="no-margin" width="560" height="315" src="$1"></iframe></figure>',
            $this->Content
        );

        // H
        $this->Content = $this->pregReplace('/h3|h4|h5|h6/i', 'h2', $this->Content);

        // Resto
        $this->Content = $this->pregReplace(
            '/<p><br><\/p>|<p><\/p>|<p> <\/p>|<p>&nbsp;<\/p>|<p lingdex="2"><br><\/p>|<p lingdex="3"><br><\/p>|<p lingdex="4"><br><\/p>|<p lingdex="5"><br><\/p>|<p lingdex="6"><br><\/p>|<p lingdex="7"><br><\/p>|<p lingdex="8"><br><\/p>|<p lingdex="9"><br><\/p>|<p lingdex="10"><br><\/p>|<p lingdex="11"><br><\/p>|<p lingdex="12"><br><\/p>|<p lingdex="13"><br><\/p>/',
            '',
            $this->Content
        );

        $this->Content = str_replace(array_keys($this->ReplaceTag), array_values($this->ReplaceTag), $this->Content);

        return $this->Content;
    }

    private function pregReplace(string $pattern, string $replacement, string $subject): string
    {

        $result = preg_replace($pattern, $replacement, $subject);

        return is_string($result) ? $result : $subject;
    }

    private function slide(): string
    {

        $this->Slider = null;
        if (is_array($this->Slides) && count($this->Slides) > 1) {
            $this->Slider = "<figure class='op-slideshow'>";
            foreach ($this->Slides as $Img) {
                $this->Slider .= '<figure>';
                $this->Slider .= sprintf("<img src='%s' />", $Img);
                $this->Slider .= '</figure>';
            }

            $this->Slider .= '</figure>';
        }

        return $this->Slider ?? '';
    }

    private function footer(): string
    {

        $this->Foot = '<footer>';

        // Artigos relacionados
        if (is_array($this->ArticleRel) && [] !== $this->ArticleRel) {
            $this->Foot .= "<ul class='op-related-articles'>";
            foreach ($this->ArticleRel as $Art) {
                $this->Foot .= sprintf("<li><a href='%s'></a></li>", $Art);
            }

            $this->Foot .= '</ul>';
        }

        // Copyright detalhes para seu artigo
        if (null !== $this->SiteName) {
            $this->Foot .= sprintf('<small>© %s</small>', $this->SiteName);
        }

        $this->Foot .= '</footer>';
        $this->Foot .= '</article>';
        $this->Foot .= '</body>';
        $this->Foot .= '</html>';

        return $this->Foot;
    }
}
