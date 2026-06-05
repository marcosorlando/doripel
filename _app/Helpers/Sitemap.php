<?php

    namespace App\Helpers;

    use App\Conn\Read;

    use function constant;
    use function curl_close;
    use function curl_exec;
    use function curl_init;
    use function curl_setopt;
    use function date;
    use function defined;
    use function file_put_contents;
    use function getenv;
    use function gzclose;
    use function gzopen;
    use function gzwrite;
    use function in_array;
    use function is_array;
    use function is_bool;
    use function is_int;
    use function is_string;
    use function strtolower;
    use function strtotime;
    use function time;
    use function urlencode;

    /**
     * Sitemap.class [ HELPER ]
     * Classe responável por gerar Sitemaps e RSS feeds para o site e o sistema!
     * @copyright (c) 2025, Marcos Orlando - ZEN AGÊNCIA WEB
     */
    class Sitemap
    {
        private string $sitemap = '';

        public function exeSitemap(bool $ping = true): void
        {

            $this->sitemapUpdate();
            if ($ping) {
                $this->sitemapPing();
            }
        }

        private function sitemapUpdate(): void
        {

            $Read = new Read();

            $this->sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
            $this->sitemap .= '<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>' . "\r\n";
            $this->sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\r\n";

            // HOME
            $this->sitemap .= '<url>' . "\r\n";
            $this->sitemap .= '<loc>' . BASE . '</loc>' . "\r\n";
            $this->sitemap .= '<lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>' . "\r\n";
            $this->sitemap .= '<changefreq>daily</changefreq>' . "\r\n";
            $this->sitemap .= '<priority>1.0</priority >' . "\r\n";
            $this->sitemap .= '</url>' . "\r\n";

            if ($this->isFeatureEnabled('APP_PAGES')) {
                // PAGES
                $Read->fullRead(
                    'SELECT page_name, created_at FROM ' . DB_PAGES . ' WHERE page_status = 1 ORDER BY page_title ASC'
                );
                $pages = $Read->getResult();
                if (is_array($pages) && [] !== $pages) {
                    /**
                     * @var array<int, array{page_name: mixed, page_date: mixed}> $pages
                     */
                    foreach ($pages as $ReadPages) {
                        $pageName = $ReadPages['page_name'] ?? null;
                        if (!is_string($pageName) || '' === $pageName) {
                            continue;
                        }

                        $pageDateRaw = $ReadPages['created_at'] ?? null;
                        $pageTimestamp = is_string($pageDateRaw) ? strtotime($pageDateRaw) : false;
                        if (false === $pageTimestamp) {
                            $pageTimestamp = time();
                        }

                        $this->sitemap .= '<url>' . "\r\n";
                        $this->sitemap .= '<loc>' . BASE . '/' . $pageName . '</loc>' . "\r\n";
                        $this->sitemap .= '<lastmod>' . date('Y-m-d\TH:i:sP', $pageTimestamp) . '</lastmod>' . "\r\n";
                        $this->sitemap .= '<changefreq>monthly</changefreq>' . "\r\n";
                        $this->sitemap .= '<priority>0.5</priority >' . "\r\n";
                        $this->sitemap .= '</url>' . "\r\n";
                    }
                }
            }

            if ($this->isFeatureEnabled('APP_POSTS')) {
                // CATEGORIES
                $Read->fullRead(
                    'SELECT category_date, category_name FROM ' . DB_CATEGORIES . ' ORDER BY category_title ASC'
                );
                $categories = $Read->getResult();
                if (is_array($categories) && [] !== $categories) {
                    /**
                     * @var array<int, array{category_date: mixed, category_name: mixed}> $categories
                     */
                    foreach ($categories as $ReadPages) {
                        $categoryName = $ReadPages['category_name'] ?? null;
                        if (!is_string($categoryName) || '' === $categoryName) {
                            continue;
                        }

                        $categoryDateRaw = $ReadPages['category_date'] ?? null;
                        $categoryTimestamp = is_string($categoryDateRaw) ? strtotime($categoryDateRaw) : false;
                        if (false === $categoryTimestamp) {
                            $categoryTimestamp = time();
                        }

                        $this->sitemap .= '<url>' . "\r\n";
                        $this->sitemap .= '<loc>' . BASE . '/artigos/' . $categoryName . '</loc>' . "\r\n";
                        $this->sitemap .= '<lastmod>' . date(
                                'Y-m-d\TH:i:sP',
                                $categoryTimestamp
                            ) . '</lastmod>' . "\r\n";
                        $this->sitemap .= '<changefreq>monthly</changefreq>' . "\r\n";
                        $this->sitemap .= '<priority>0.7</priority >' . "\r\n";
                        $this->sitemap .= '</url>' . "\r\n";
                    }
                }

                // POSTS
                $Read->fullRead(
                    'SELECT post_name, post_date FROM ' . DB_POSTS . ' WHERE post_status = 1 AND post_date <= NOW() ORDER BY post_date DESC'
                );
                $posts = $Read->getResult();
                if (is_array($posts) && [] !== $posts) {
                    /**
                     * @var array<int, array{post_name: mixed, post_date: mixed}> $posts
                     */
                    foreach ($posts as $ReadPages) {
                        $postName = $ReadPages['post_name'] ?? null;
                        if (!is_string($postName) || '' === $postName) {
                            continue;
                        }

                        $postDateRaw = $ReadPages['post_date'] ?? null;
                        $postTimestamp = is_string($postDateRaw) ? strtotime($postDateRaw) : false;
                        if (false === $postTimestamp) {
                            $postTimestamp = time();
                        }

                        $this->sitemap .= '<url>' . "\r\n";
                        $this->sitemap .= '<loc>' . BASE . '/artigo/' . $postName . '</loc>' . "\r\n";
                        $this->sitemap .= '<lastmod>' . date('Y-m-d\TH:i:sP', $postTimestamp) . '</lastmod>' . "\r\n";
                        $this->sitemap .= '<changefreq>weekly</changefreq>' . "\r\n";
                        $this->sitemap .= '<priority>0.9</priority >' . "\r\n";
                        $this->sitemap .= '</url>' . "\r\n";
                    }
                }
            }

            if ($this->isFeatureEnabled('APP_PRODUCTS_TRAVI')) {
                // PRODUCTS CATEGORIES
                $Read->fullRead('SELECT cat_name, cat_created FROM ' . DB_PDT_CATS_TRAVI . ' ORDER BY cat_title ASC');
                $productCategories = $Read->getResult();
                if (is_array($productCategories) && [] !== $productCategories) {
                    /**
                     * @var array<int, array{cat_name: mixed, cat_created: mixed}> $productCategories
                     */
                    foreach ($productCategories as $ReadPages) {
                        $categoryName = $ReadPages['cat_name'] ?? null;
                        if (!is_string($categoryName) || '' === $categoryName) {
                            continue;
                        }

                        $categoryCreatedRaw = $ReadPages['cat_created'] ?? null;
                        $categoryTimestamp = is_string($categoryCreatedRaw) ? strtotime($categoryCreatedRaw) : false;
                        if (false === $categoryTimestamp) {
                            $categoryTimestamp = time();
                        }

                        $this->sitemap .= '<url>' . "\r\n";
                        $this->sitemap .= '<loc>' . BASE . '/produtos/' . $categoryName . '</loc>' . "\r\n";
                        $this->sitemap .= '<lastmod>' . date(
                                'Y-m-d\TH:i:sP',
                                $categoryTimestamp
                            ) . '</lastmod>' . "\r\n";
                        $this->sitemap .= '<changefreq>weekly</changefreq>' . "\r\n";
                        $this->sitemap .= '<priority>0.9</priority >' . "\r\n";
                        $this->sitemap .= '</url>' . "\r\n";
                    }
                }

                // PRODUTCTS
                $Read->fullRead('SELECT pdt_name, pdt_created FROM ' . DB_PDT_TRAVI . ' ORDER BY pdt_created DESC');
                $products = $Read->getResult();
                if (is_array($products) && [] !== $products) {
                    /**
                     * @var array<int, array{pdt_name: mixed, pdt_created: mixed}> $products
                     */
                    foreach ($products as $ReadPages) {
                        $productName = $ReadPages['pdt_name'] ?? null;
                        if (!is_string($productName) || '' === $productName) {
                            continue;
                        }

                        $productCreatedRaw = $ReadPages['pdt_created'] ?? null;
                        $productTimestamp = is_string($productCreatedRaw) ? strtotime($productCreatedRaw) : false;
                        if (false === $productTimestamp) {
                            $productTimestamp = time();
                        }

                        $this->sitemap .= '<url>' . "\r\n";
                        $this->sitemap .= '<loc>' . BASE . '/produto/' . $productName . '</loc>' . "\r\n";
                        $this->sitemap .= '<lastmod>' . date(
                                'Y-m-d\TH:i:sP',
                                $productTimestamp
                            ) . '</lastmod>' . "\r\n";
                        $this->sitemap .= '<changefreq>weekly</changefreq>' . "\r\n";
                        $this->sitemap .= '<priority>0.9</priority >' . "\r\n";
                        $this->sitemap .= '</url>' . "\r\n";
                    }
                }

                // PRODUCTS BRANDS
                /*  $Read->fullRead("SELECT brand_name, brand_created FROM " . DB_PDT_BRANDS . " ORDER BY brand_title ASC");
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $ReadPages) {
                            $this->sitemap .= '<url>' . "\r\n";
                            $this->sitemap .= '<loc>' . BASE . '/marca/' . $ReadPages['brand_name'] . '</loc>' . "\r\n";
                            $this->sitemap .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($ReadPages['brand_created'])) . '</lastmod>' . "\r\n";
                            $this->sitemap .= '<changefreq>weekly</changefreq>' . "\r\n";
                            $this->sitemap .= '<priority>0.9</priority >' . "\r\n";
                            $this->sitemap .= '</url>' . "\r\n";
                        }
                    } */
            }

            // CLOSE
            $this->sitemap .= '</urlset>';

            // CRIA O XML
            file_put_contents('../sitemap.xml', $this->sitemap);

            // CRIA O GZ
            $sitemapGz = gzopen('../sitemap.xml.gz', 'w9');
            if (false !== $sitemapGz) {
                gzwrite($sitemapGz, $this->sitemap);
                gzclose($sitemapGz);
            }
        }

        private function isFeatureEnabled(string $configName): bool
        {

            if (defined($configName)) {
                $value = constant($configName);
                if (is_bool($value)) {
                    return $value;
                }

                if (is_int($value)) {
                    return 1 === $value;
                }

                if (is_string($value)) {
                    $normalized = strtolower($value);

                    return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
                }
            }

            $envValue = getenv($configName);
            if (false === $envValue) {
                return false;
            }

            $normalized = strtolower($envValue);

            return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
        }

        private function sitemapPing(): void
        {

            $sitemapUrl = BASE . '/sitemap.xml';
            $endpoints = [
                'https://www.google.com/webmasters/tools/ping?sitemap=' . urlencode($sitemapUrl),
                'https://www.bing.com/webmaster/ping.aspx?siteMap=' . urlencode($sitemapUrl),
            ];

            foreach ($endpoints as $url) {
                $ch = curl_init($url);
                if (false === $ch) {
                    continue;
                }

                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
            }
        }
    }
