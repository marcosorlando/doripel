<?php

namespace App\Models;

use App\Conn\Read;

use function array_map;
use function constant;
use function defined;
use function explode;
use function in_array;
use function is_array;
use function is_numeric;
use function is_string;
use function rtrim;
use function sprintf;
use function str_pad;
use function strip_tags;
use function trim;

/**
 * Seo [ MODEL ]
 * Classe de apoio para o modelo link. Pode ser utilizada para gerar SEO para as páginas do sistema!
 * @copyright (c) 2025, Marcos Orlando - ZEN AGÊNCIA WEB
 */
class Seo
{
    /**
     * @var array<int, null|string>
     */
    private array $pach = [];

    private ?string $file = null;

    private ?string $link = null;

    private ?string $key = null;

    private ?string $schema = null;

    private ?string $title = null;

    private ?string $description = null;

    private ?string $image = null;

    private ?string $data = null;

    public function __construct(string $pach)
    {

        $parts = explode('/', strip_tags(trim($pach)));
        $this->pach = array_map(
            fn(string $value): ?string => $this->stringValue($value),
            $parts
        );
        $this->file = $this->normalizePathSegment($this->pach[0] ?? null);
        $this->link = $this->normalizePathSegment($this->pach[1] ?? null);
        $this->key = $this->normalizePathSegment($this->pach[2] ?? null);

        $this->setpach();
    }

    private function stringValue(mixed $value): ?string
    {

        if (null === $value) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return '' === $trimmed ? null : $trimmed;
        }

        if (is_numeric($value)) {
            return (string)$value;
        }

        return null;
    }

    private function normalizePathSegment(mixed $segment): ?string
    {

        return $this->stringValue($segment);
    }

    private function setpach(): void
    {

        $read = new Read();

        $pages = $this->fetchColumnValues($read, 'SELECT page_name FROM ' . DB_PAGES, 'page_name');
        $landingPages = $this->fetchColumnValues($read, 'SELECT page_name FROM ' . DB_LANDING_PAGES, 'page_name');
        $thankYouPages = $this->fetchColumnValues($read, 'SELECT page_name FROM ' . DB_THANKYOU_PAGES, 'page_name');
        $cards = $this->fetchColumnValues($read, 'SELECT carduser_url FROM ' . DB_CARD_USER, 'carduser_url');

        if (null !== $this->file && in_array($this->file, $pages, true)) {
            $page = $this->fetchSingleRow(
                $read,
                'SELECT page_title, page_subtitle, page_cover FROM ' . DB_PAGES . ' WHERE page_name = :nm',
                'nm=' . $this->file
            );
            if (null !== $page) {
                $pageTitle = $this->stringValue($page['page_title'] ?? null);
                $pageSubtitle = $this->stringValue($page['page_subtitle'] ?? null);
                $pageCover = $this->stringValue($page['page_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($pageTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $pageSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($pageCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if (null !== $this->file && in_array($this->file, $landingPages, true)) {
            $page = $this->fetchSingleRow(
                $read,
                'SELECT page_title, page_subtitle, page_cover FROM ' . DB_LANDING_PAGES . ' WHERE page_name = :nm',
                'nm=' . $this->file
            );
            if (null !== $page) {
                $pageTitle = $this->stringValue($page['page_title'] ?? null);
                $pageSubtitle = $this->stringValue($page['page_subtitle'] ?? null);
                $pageCover = $this->stringValue($page['page_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($pageTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $pageSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($pageCover, '/uploads/landingpages/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if (null !== $this->file && in_array($this->file, $thankYouPages, true)) {
            $page = $this->fetchSingleRow(
                $read,
                'SELECT page_title, page_cover FROM ' . DB_THANKYOU_PAGES . ' WHERE page_name = :nm',
                'nm=' . $this->file
            );
            if (null !== $page) {
                $pageTitle = $this->stringValue($page['page_title'] ?? null);
                $pageCover = $this->stringValue($page['page_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    $pageTitle ?? SITE_NAME,
                    'Download do conteúdo: ' . ($pageTitle ?? SITE_NAME),
                    $this->resolveImagePath($pageCover, '/uploads/thankyoupages/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if (null !== $this->file && in_array($this->file, $cards, true)) {
            $page = $this->fetchSingleRow(
                $read,
                'SELECT carduser_name, carduser_lastname, carduser_cargo, carduser_thumb FROM ' . DB_CARD_USER . ' WHERE carduser_url = :nm',
                'nm=' . $this->file
            );
            if (null !== $page) {
                $firstName = $this->stringValue($page['carduser_name'] ?? null) ?? '';
                $lastName = $this->stringValue($page['carduser_lastname'] ?? null) ?? '';
                $fullName = trim($firstName . ' ' . $lastName);
                $cargo = $this->stringValue($page['carduser_cargo'] ?? null);
                $thumb = $this->stringValue($page['carduser_thumb'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    sprintf('Cartão de Visita - %s', '' !== $fullName ? $fullName : SITE_NAME),
                    $cargo ?? SITE_DESC,
                    $this->resolveImagePath($thumb, '/uploads/linktree/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('index' === $this->file) {
            $this->assignSeo('WebSite', SITE_NAME . ' - ' . SITE_SUBNAME, SITE_DESC, $this->defaultImagePath());

            return;
        }

        if ('artigo' === $this->file && 'amp' === $this->key) {
            $post = $this->fetchSingleRow(
                $read,
                'SELECT post_title, post_subtitle, post_cover, post_date FROM ' . DB_POSTS . ' WHERE post_name = :nm AND post_date <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $post) {
                $postTitle = $this->stringValue($post['post_title'] ?? null);
                $postSubtitle = $this->stringValue($post['post_subtitle'] ?? null);
                $postCover = $this->stringValue($post['post_cover'] ?? null);
                $postDate = $this->stringValue($post['post_date'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($postTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $postSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($postCover, '/uploads/')
                );
                $this->data = $postDate;

                return;
            }

            $this->set404();

            return;
        }

        if ('artigo' === $this->file) {
            $post = $this->fetchSingleRow(
                $read,
                'SELECT post_title, post_subtitle, post_cover FROM ' . DB_POSTS . ' WHERE post_name = :nm AND post_date <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $post) {
                $postTitle = $this->stringValue($post['post_title'] ?? null);
                $postSubtitle = $this->stringValue($post['post_subtitle'] ?? null);
                $postCover = $this->stringValue($post['post_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($postTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $postSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($postCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('artigos' === $this->file) {
            $category = $this->fetchSingleRow(
                $read,
                'SELECT category_title, category_content FROM ' . DB_CATEGORIES . ' WHERE category_name = :nm',
                'nm=' . $this->link
            );
            if (null !== $category) {
                $categoryTitle = $this->stringValue($category['category_title'] ?? null);
                $categoryContent = $this->stringValue($category['category_content'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($categoryTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $categoryContent ?? SITE_DESC,
                    $this->defaultImagePath()
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('pesquisa' === $this->file) {
            $keyword = $this->link ?? '';
            $this->assignSeo(
                'WebSite',
                sprintf('Pesquisa por %s em %s', $keyword, SITE_NAME),
                SITE_DESC,
                $this->defaultImagePath()
            );

            return;
        }

        if ('produto' === $this->file) {
            $product = $this->fetchSingleRow(
                $read,
                'SELECT pdt_title, pdt_subtitle, pdt_cover FROM ' . DB_PDT_TRAVI . ' WHERE pdt_name = :nm AND pdt_created <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $product) {
                $productTitle = $this->stringValue($product['pdt_title'] ?? null);
                $productSubtitle = $this->stringValue($product['pdt_subtitle'] ?? null);
                $productCover = $this->stringValue($product['pdt_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($productTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $productSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($productCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('produtos' === $this->file) {
            $category = $this->fetchSingleRow(
                $read,
                'SELECT cat_title, cat_description FROM ' . DB_PDT_CATS_TRAVI . ' WHERE cat_name = :nm',
                'nm=' . $this->link
            );
            if (null !== $category) {
                $categoryTitle = $this->stringValue($category['cat_title'] ?? null);
                $categoryDescription = $this->stringValue($category['cat_description'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($categoryTitle ?? 'Produtos') . ' - ' . SITE_NAME,
                    $categoryDescription ?? SITE_DESC,
                    $this->defaultImagePath()
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('pesquisa-produtos' === $this->file) {
            $keyword = $this->link ?? '';
            $this->assignSeo(
                'WebSite',
                sprintf('Pesquisa por %s em %s', $keyword, SITE_NAME),
                SITE_DESC,
                $this->defaultImagePath()
            );

            return;
        }

        if ('servico' === $this->file) {
            $service = $this->fetchSingleRow(
                $read,
                'SELECT svc_title, svc_subtitle, svc_cover FROM ' . DB_SVC . ' WHERE svc_name = :nm AND svc_created <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $service) {
                $serviceTitle = $this->stringValue($service['svc_title'] ?? null);
                $serviceSubtitle = $this->stringValue($service['svc_subtitle'] ?? null);
                $serviceCover = $this->stringValue($service['svc_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($serviceTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $serviceSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($serviceCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('servicos' === $this->file) {
            $this->assignSeo(
                'WebSite',
                'Serviços - ' . SITE_NAME,
                'Serviços prestados pela Travi.',
                $this->defaultImagePath()
            );

            return;
        }

        if ('segmento' === $this->file) {
            $segment = $this->fetchSingleRow(
                $read,
                'SELECT seg_title, seg_subtitle, seg_cover FROM ' . DB_SEG . ' WHERE seg_name = :nm AND seg_created <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $segment) {
                $segmentTitle = $this->stringValue($segment['seg_title'] ?? null);
                $segmentSubtitle = $this->stringValue($segment['seg_subtitle'] ?? null);
                $segmentCover = $this->stringValue($segment['seg_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($segmentTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $segmentSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($segmentCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('segmentos' === $this->file) {
            $this->assignSeo(
                'WebSite',
                'Segmentos - ' . SITE_NAME,
                'Segmentos que a Travi atende.',
                $this->defaultImagePath()
            );

            return;
        }

        if ('certificacao' === $this->file) {
            $cert = $this->fetchSingleRow(
                $read,
                'SELECT cert_title, cert_subtitle, cert_cover FROM ' . DB_CERT . ' WHERE cert_name = :nm AND cert_created <= NOW()',
                'nm=' . $this->link
            );
            if (null !== $cert) {
                $certTitle = $this->stringValue($cert['cert_title'] ?? null);
                $certSubtitle = $this->stringValue($cert['cert_subtitle'] ?? null);
                $certCover = $this->stringValue($cert['cert_cover'] ?? null);

                $this->assignSeo(
                    'WebSite',
                    ($certTitle ?? SITE_NAME) . ' - ' . SITE_NAME,
                    $certSubtitle ?? SITE_DESC,
                    $this->resolveImagePath($certCover, '/uploads/')
                );

                return;
            }

            $this->set404();

            return;
        }

        if ('certificacoes' === $this->file) {
            $this->assignSeo(
                'WebSite',
                'Certificações - ' . SITE_NAME,
                'Certificações que a Travi possui.',
                $this->defaultImagePath()
            );

            return;
        }

        if ('conta' === $this->file) {
            $accountManagerRaw = defined('ACC_MANAGER') ? constant('ACC_MANAGER') : null;
            $accountManagerEnabled = in_array($accountManagerRaw, [1, '1', true, 'true', 'on', 'yes'], true);
            if ($accountManagerEnabled) {
                $orderNumber = $this->key ?? '';
                $arrAccountApp = [
                    '' => 'Entrar!',
                    'login' => 'Entrar!',
                    'cadastro' => 'Criar Conta!',
                    'recuperar' => 'Recuperar Senha!',
                    'nova-senha' => 'Criar Nova Senha!',
                    'sair' => 'Sair!',
                    'home' => 'Minha Conta!',
                    'restrito' => 'Acesso Restrito!',
                    'enderecos' => 'Meus Endereços!',
                    'pedidos' => 'Meus Pedidos!',
                    'dados' => 'Atualizar Dados!',
                    'pedido' => 'Pedido #' . str_pad($orderNumber, 7, '0', STR_PAD_LEFT),
                ];

                $titleKey = $this->link ?? '';
                $title = $arrAccountApp[$titleKey] ?? 'OPPPSSS!';
                $this->assignSeo('WebSite', SITE_NAME . ' - ' . $title, SITE_DESC, $this->defaultImagePath());

                return;
            }

            $this->set404();

            return;
        }

        $this->set404();
    }

    // PRIVATE METHODS

    /**
     * @return list<string>
     */
    private function fetchColumnValues(Read $read, string $query, string $column): array
    {

        $read->fullRead($query);
        $result = $read->getResult();
        if (!is_array($result) || [] === $result) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $result */
        $values = [];
        foreach ($result as $row) {
            $value = $this->stringValue($row[$column] ?? null);
            if (null !== $value) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @return null|array<string, mixed>
     */
    private function fetchSingleRow(Read $read, string $query, string $parseString): ?array
    {

        $read->fullRead($query, $parseString);

        return $this->firstRow($read->getResult());
    }

    /**
     * @param null|array<int, array<string, mixed>> $result
     *
     * @return null|array<string, mixed>
     */
    private function firstRow(?array $result): ?array
    {

        if (!is_array($result) || [] === $result) {
            return null;
        }

        return $result[0];
    }

    private function assignSeo(string $schema, string $title, string $description, string $imagePath): void
    {

        $this->schema = $schema;
        $this->title = $title;
        $this->description = $description;
        $this->image = $imagePath;
    }

    private function resolveImagePath(?string $fileName, string $prefix): string
    {

        if (null === $fileName || '' === $fileName) {
            return $this->defaultImagePath();
        }

        $normalizedPrefix = rtrim($prefix, '/');

        return BASE . $normalizedPrefix . '/' . $fileName;
    }

    private function defaultImagePath(): string
    {

        return INCLUDE_PATH . '/images/default.jpg';
    }

    private function set404(): void
    {

        $this->schema = 'WebSite';
        $this->title = 'Oppsss, nada encontrado! - ' . SITE_NAME;
        $this->description = SITE_DESC;
        $this->image = $this->defaultImagePath();
    }

    public function getschema(): ?string
    {

        return $this->schema;
    }

    public function gettitle(): ?string
    {

        return $this->title;
    }

    public function getdescription(): ?string
    {

        return $this->description;
    }

    public function getimage(): ?string
    {

        return $this->image;
    }

    public function getdata(): ?string
    {

        return $this->data;
    }
}
