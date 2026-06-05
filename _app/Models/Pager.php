<?php

    declare(strict_types=1);

    namespace App\Models;

    use App\Conn\Read;

    use function ceil;
    use function header;
    use function headers_sent;
    use function htmlspecialchars;
    use function max;
    use function sprintf;

    /**
     * Class Pager
     * Helper para paginação de resultados.
     * Compatível com PHP 8.3, PSR-12 e tipagem forte.
     * @author  ZEN AGÊNCIA WEB
     */
    class Pager
    {
        // Controle de página
        private int $page = 1;

        private int $limit = 10;

        private int $offset = 0;

        // Fonte de dados
        private string $table = '';

        private ?string $terms = null;

        private ?string $places = null;

        private ?string $query = null;

        // Dados e saída
        private int $rows = 0;

        private string $paginator = '';

        private readonly int $maxLinks;

        private readonly string $first;

        private readonly string $last;

        private string $hash = '';

        private readonly ?int $total;

        // Dependência de leitura (injeção para testes)
        private readonly Read $reader;

        /**
         * @param string $link Ex.: 'index.php?pagina&page=' (deve terminar no separador antes do número)
         * @param null|string $first Texto do link da primeira página
         * @param null|string $last Texto do link da última página
         * @param null|int $maxLinks Quantidade de links intermediários (padrão 5)
         * @param null|int $total Total de registros (opcional; usado por returnPage)
         * @param null|Read $reader Dependência de leitura (opcional; por padrão será instanciada)
         */
        public function __construct(
            private readonly string $link,
            ?string $first = null,
            ?string $last = null,
            ?int $maxLinks = null,
            $total = null,
            $reader = null
        ) {

            $this->first = (null !== $first && '' !== $first && '0' !== $first) ? $first : '<i class="icon-first btn_notext"></i>';
            $this->last = (null !== $last && '' !== $last && '0' !== $last) ? $last : '<i class="icon-last btn_notext"></i>';
            $this->maxLinks = (null !== $maxLinks && $maxLinks > 0) ? $maxLinks : 5;

            // Coerce total to int when possible, accept empty strings for backward compatibility
            $this->total = (null !== $total && '' !== $total && is_numeric($total)) ? (int)$total : null;

            // Accept a Read instance or fallback to a new Read(). Some call sites pass '' or other values.
            $this->reader = ($reader instanceof Read) ? $reader : new Read();
        }

        /**
         * Definir página e limite. Use getLimit()/getOffset() na consulta SQL.
         */
        public function exePager(int $page, int $limit): void
        {

            $this->page = ($page > 0) ? $page : 1;
            $this->limit = max(1, $limit);
            $this->offset = ($this->page * $this->limit) - $this->limit;
        }

        /**
         * Se a página atual for inválida e maior que o total, faz redirect para a anterior
         * (ou para a última, quando $total é conhecido).
         * Mantido por compatibilidade. Preferir buildOutOfRangeRedirectUrl() para evitar efeito colateral.
         */
        public function returnPage(): void
        {

            if ($this->page <= 1) {
                return;
            }

            $targetPage = null !== $this->total
                ? (int)ceil($this->total / $this->limit)
                : $this->page - 1;

            $url = $this->link . $targetPage;

            if (!headers_sent()) {
                header('Location: ' . $url);

                exit;
            }
            // Caso os headers já tenham sido enviados, não há o que fazer aqui sem quebrar a página.
        }

        /**
         * Alternativa sem efeitos colaterais: retorna a URL adequada caso a página esteja fora do intervalo.
         */
        public function buildOutOfRangeRedirectUrl(): ?string
        {

            if ($this->page <= 1) {
                return null;
            }

            $targetPage = null !== $this->total
                ? (int)ceil($this->total / $this->limit)
                : $this->page - 1;

            return $this->link . $targetPage;
        }

        public function getPage(): int
        {

            return $this->page;
        }

        public function getLimit(): int
        {

            return $this->limit;
        }

        public function getOffset(): int
        {

            return $this->offset;
        }

        /**
         * Monta a paginação com base em tabela/termos/places.
         *
         * @param null|string $hash Ex.: '#ancora'
         */
        public function exePaginator(
            string $table,
            ?string $terms = null,
            ?string $parseString = null,
            ?string $hash = null
        ): void {

            $this->table = $table;
            $this->terms = $terms;
            $this->places = $parseString;
            $this->hash = $hash ?? '';
            $this->buildPaginator();
        }

        /**
         * ===== Internals =====.
         */
        private function buildPaginator(): void
        {

            // Executa a leitura conforme a estratégia definida
            if (null === $this->query) {
                // exeRead(string $table, ?string $terms, ?string $places)
                $this->reader->exeRead($this->table, $this->terms, $this->places);
            } else {
                // fullRead(string $query, ?string $places)
                $this->reader->fullRead($this->query, $this->places);
            }

            $this->rows = $this->reader->getRowCount();
            $this->paginator = '';

            if ($this->rows <= $this->limit) {
                return; // Não há várias páginas
            }

            $totalPages = ceil($this->rows / $this->limit);
            $maxLinks = $this->maxLinks;
            $hash = $this->hash;

            $html = "<ul class='paginator pagination'>";

            // Primeira
            $titleFirst = htmlspecialchars(strip_tags($this->first), ENT_QUOTES, 'UTF-8');
            $labelFirst = ('' !== $this->first && (false !== strpos($this->first, '<') || false !== strpos(
                        $this->first,
                        '>'
                    ))) ? $this->first : htmlspecialchars($this->first, ENT_QUOTES, 'UTF-8');
            $html .= sprintf(
                '<li><a title="%s" href="%s1%s">%s</a></li>',
                $titleFirst,
                $this->link,
                $hash,
                $labelFirst
            );

            // Anteriores
            for ($i = $this->page - $maxLinks; $i <= $this->page - 1; ++$i) {
                if ($i >= 1) {
                    $html .= sprintf(
                        '<li><a title="Página %d" href="%s%d%s">%d</a></li>',
                        $i,
                        $this->link,
                        $i,
                        $hash,
                        $i
                    );
                }
            }

            // Atual
            $html .= sprintf('<li class="active"><a>%d</a></li>', $this->page);

            // Próximas
            for ($i = $this->page + 1; $i <= $this->page + $maxLinks; ++$i) {
                if ($i <= $totalPages) {
                    $html .= sprintf(
                        '<li><a title="Página %d" href="%s%d%s">%d</a></li>',
                        $i,
                        $this->link,
                        $i,
                        $hash,
                        $i
                    );
                }
            }

            // Última
            $titleLast = htmlspecialchars(strip_tags($this->last), ENT_QUOTES, 'UTF-8');
            $labelLast = ('' !== $this->last && (false !== strpos($this->last, '<') || false !== strpos(
                        $this->last,
                        '>'
                    ))) ? $this->last : htmlspecialchars($this->last, ENT_QUOTES, 'UTF-8');
            $html .= sprintf(
                '<li><a title="%s" href="%s%d%s">%s</a></li>',
                $titleLast,
                $this->link,
                $totalPages,
                $hash,
                $labelLast
            );

            $html .= '</ul>';

            $this->paginator = $html;
        }

        /**
         * Monta a paginação com base em uma SQL completa.
         */
        public function exeFullPaginator(string $query, ?string $parseString = null, ?string $hash = null): void
        {

            $this->query = $query;
            $this->places = $parseString;
            $this->hash = $hash ?? '';
            $this->buildPaginator();
        }

        /**
         * HTML da paginação.
         */
        public function getPaginator(): string
        {

            return $this->paginator;
        }
    }
