<?php

    declare(strict_types=1);

    namespace App\Helpers;

    use Normalizer;

    use function array_filter;
    use function array_slice;
    use function array_values;
    use function checkdate;
    use function count;
    use function date;
    use function explode;
    use function filter_var;
    use function header;
    use function htmlspecialchars;
    use function iconv;
    use function implode;
    use function is_array;
    use function is_int;
    use function is_string;
    use function mb_convert_case;
    use function mb_convert_encoding;
    use function mb_detect_encoding;
    use function mb_internal_encoding;
    use function mb_strlen;
    use function mb_strrpos;
    use function mb_strtolower;
    use function mb_substr;
    use function parse_str;
    use function parse_url;
    use function preg_match;
    use function preg_replace;
    use function preg_split;
    use function random_int;
    use function rawurlencode;
    use function sprintf;
    use function str_contains;
    use function str_replace;
    use function str_starts_with;
    use function strip_tags;
    use function strlen;
    use function strtok;
    use function strtolower;
    use function trim;
    use function ucwords;

    mb_internal_encoding('UTF-8');

    class Check
    {
        /**
         * Valida um endereço de e-mail.
         */
        public static function email(string $email): bool
        {

            return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        /**
         * Valida um CPF (com ou sem pontuação).
         */
        public static function cpf(string $cpf): bool
        {

            $digits = preg_replace('/[^0-9]/', '', $cpf);
            if (null === $digits || 11 !== strlen($digits)) {
                return false;
            }

            if (1 === preg_match('/^(\d)\1{10}$/', $digits)) {
                return false;
            }

            $digitoA = 0;
            $digitoB = 0;
            for ($i = 0, $x = 10; $i <= 8; $i++, $x--) {
                $digitoA += (int)$digits[$i] * $x;
            }

            for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
                $digitoB += (int)$digits[$i] * $x;
            }

            $somaA = (($digitoA % 11) < 2) ? 0 : 11 - ($digitoA % 11);
            $somaB = (($digitoB % 11) < 2) ? 0 : 11 - ($digitoB % 11);

            return $somaA === (int)$digits[9] && $somaB === (int)$digits[10];
        }

        /**
         * Valida um CNPJ (com ou sem pontuação).
         */
        public static function cnpj(string $cnpj): bool
        {

            $digits = preg_replace('/[^0-9]/', '', $cnpj);
            if (null === $digits || 14 !== strlen($digits)) {
                return false;
            }

            if (1 === preg_match('/^(\d)\1{13}$/', $digits)) {
                return false;
            }

            $a = 0;
            $b = 0;
            for ($i = 0, $c = 5; $i <= 11; $i++, $c--) {
                $c = (1 === $c ? 9 : $c);
                $a += (int)$digits[$i] * $c;
            }

            for ($i = 0, $c = 6; $i <= 12; $i++, $c--) {
                $c = (1 === $c ? 9 : $c);
                $b += (int)$digits[$i] * $c;
            }

            $somaA = (($a % 11) < 2) ? 0 : 11 - ($a % 11);
            $somaB = (($b % 11) < 2) ? 0 : 11 - ($b % 11);

            return $somaA === (int)$digits[12] && $somaB === (int)$digits[13];
        }

        /**
         * Converte uma data no formato brasileiro (d/m/Y) para o padrão ISO (Y-m-d).
         */
        public static function nascimento(string $data): ?string
        {

            $parts = explode(' ', trim($data));
            $dateParts = explode('/', $parts[0]);
            if (3 !== count($dateParts)) {
                return null;
            }

            [$d, $m, $y] = $dateParts;
            if (!checkdate((int)$m, (int)$d, (int)$y)) {
                return null;
            }

            return sprintf('%04d-%02d-%02d', (int)$y, (int)$m, (int)$d);
        }

        /**
         * Converte uma data (d/m/Y ou d/m/Y H:i:s) para o formato datetime padrão (Y-m-d H:i:s).
         */
        public static function data(string $data): ?string
        {

            $parts = explode(' ', trim($data));
            $dateParts = explode('/', $parts[0]);
            if (3 !== count($dateParts)) {
                return null;
            }

            [$d, $m, $y] = $dateParts;
            if (!checkdate((int)$m, (int)$d, (int)$y)) {
                return null;
            }

            $time = $parts[1] ?? date('H:i:s');

            return sprintf('%04d-%02d-%02d %s', (int)$y, (int)$m, (int)$d, $time);
        }

        /**
         * Limita o conteúdo a uma quantidade de palavras.
         */
        public static function words(string $string, int $limit, ?string $pointer = null): string
        {

            $clean = strip_tags(trim($string));
            $words = preg_split('/\s+/', $clean);
            if (false === $words) {
                $words = [];
            }

            if (count($words) <= $limit) {
                return $clean;
            }

            $new = implode(' ', array_slice($words, 0, $limit));
            $suffix = null === $pointer || '' === $pointer ? '...' : (' ' . $pointer);

            return $new . $suffix;
        }

        /**
         * Limita o conteúdo a uma quantidade de caracteres, respeitando a quebra de palavra.
         */
        public static function chars(string $string, int $limit): string
        {

            $clean = strip_tags($string);
            if (mb_strlen($clean) <= $limit) {
                return $clean;
            }

            $substr = mb_substr($clean, 0, $limit);
            $lastSpace = mb_strrpos($substr, ' ');
            if (false !== $lastSpace) {
                $substr = mb_substr($substr, 0, $lastSpace);
            }

            return $substr . '...';
        }

        /**
         * Retorna a URL da miniatura do YouTube para o ID informado ou detectado.
         */
        public static function youtubeThumbnailUrl(string $value, string $quality = 'hqdefault'): ?string
        {

            $videoId = self::youtubeVideoId($value);
            if (null === $videoId) {
                return null;
            }

            $quality = '' === trim($quality) ? 'hqdefault' : $quality;

            return sprintf('https://img.youtube.com/vi/%s/%s.jpg', $videoId, $quality);
        }

        /**
         * Normaliza o identificador do vídeo do YouTube, aceitando URL completa ou apenas o ID bruto.
         */
        public static function youtubeVideoId(string $value): ?string
        {

            $candidate = trim($value);
            if ('' === $candidate) {
                return null;
            }

            if (1 === preg_match('/^[\w-]{11}\z/', $candidate)) {
                return $candidate;
            }

            if (false === filter_var($candidate, FILTER_VALIDATE_URL)) {
                return null;
            }

            $urlParts = parse_url($candidate);
            if (!is_array($urlParts)) {
                return null;
            }

            $host = strtolower($urlParts['host'] ?? '');
            $path = trim($urlParts['path'] ?? '', '/');

            if ('youtu.be' === $host) {
                $id = strtok($path, '/?&#');
                if (false !== $id && 1 === preg_match('/^[\w-]{11}\z/', $id)) {
                    return $id;
                }
            }

            if (str_contains($host, 'youtube.com')) {
                $query = [];
                if (isset($urlParts['query']) && '' !== $urlParts['query']) {
                    parse_str($urlParts['query'], $query);
                    $paramId = $query['v'] ?? $query['vi'] ?? null;
                    if (is_string($paramId) && 1 === preg_match('/^[\w-]{11}\z/', $paramId)) {
                        return $paramId;
                    }
                }

                $segments = array_filter(
                    explode('/', $path),
                    static fn(string $segment): bool => '' !== $segment
                );
                $segments = array_values($segments);
                foreach ($segments as $segment) {
                    if (1 === preg_match('/^[\w-]{11}\z/', $segment)) {
                        return $segment;
                    }
                }
            }

            return null;
        }

        /**
         * Monta o link do WhatsApp com uma mensagem pré-preenchida.
         */
        public static function whatsMessage(string $phoneNumber, string $message): string
        {

            // só dígitos
            $digits = preg_replace('/\D+/', '', $phoneNumber);
            if (null === $digits) {
                $digits = '';
            }

            // adiciona DDI 55 se ainda não houver
            if (!str_starts_with($digits, '55')) {
                $digits = '55' . $digits;
            }

            $digits = trim($digits);

            // codifica mensagem já em UTF-8
            $encoded = self::safeUrlEncode(self::ensureUtf8($message));

            // Pode usar wa.me ou api.whatsapp.com; aqui mantenho o formato que você usou no último exemplo
            return sprintf(
                'https://api.whatsapp.com/send/?phone=%s&text=%s&type=phone_number&app_absent=0',
                $digits,
                $encoded
            );
        }

        /**
         * URL-encode seguro para WhatsApp/URLs.
         * Normaliza quebras de linha e garante UTF-8.
         */
        public static function safeUrlEncode(?string $value): string
        {

            $s = self::ensureUtf8($value);
            $s = str_replace(["\r\n", "\r"], "\n", $s);

            return rawurlencode($s);
        }

        /**
         * Garante que a string esteja em UTF-8 (necessário para emojis não virarem �).
         */
        public static function ensureUtf8(?string $value): string
        {

            $s = $value ?? '';
            if ('' !== $s) {
                $encoding = mb_detect_encoding($s, 'UTF-8', true);
                if (false === $encoding) {
                    $s = mb_convert_encoding($s, 'UTF-8');
                }
            }

            return $s;
        }

        public static function salutation(): string
        {

            $hour = (int)date('H');

            return ($hour > 0 && $hour <= 12) ? 'Bom dia!' : (($hour > 12 && $hour <= 18) ? 'Boa tarde! ' : 'Boa noite!');
        }

        /**
         * Gera uma senha aleatória respeitando o conjunto de caracteres informado.
         */
        public static function newPass(
            int $length = 8,
            bool $useUppercase = true,
            bool $useNumbers = true,
            bool $useSymbols = false,
            bool $useLowercase = true
        ): string {

            $lmin = 'abcdefghijklmnopqrstuvwxyz';
            $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $num = '1234567890';
            $simb = '!@#$%*-';
            $result = '';
            $pool = '';

            if ($useLowercase) {
                $pool .= $lmin;
            }

            if ($useUppercase) {
                $pool .= $lmai;
            }

            if ($useNumbers) {
                $pool .= $num;
            }

            if ($useSymbols) {
                $pool .= $simb;
            }

            if ('' === $pool) {
                $pool = $lmin;
            }

            $poolLength = strlen($pool);

            for ($n = 0; $n < $length; ++$n) {
                $index = random_int(0, $poolLength - 1);
                $result .= $pool[$index];
            }

            return $result;
        }

        /**
         * @param $legend string - legenda do campo
         * @param $name   string - nome do campo input
         * @param $status bool - 0 / 1
         * @param $on     string - data-on
         * @param $off    string - data-off
         */
        public static function switchOnOff(
            string $name,
            bool|int|string $status,
            ?string $legend = null,
            string $on = 'ON',
            string $off = 'OFF'
        ): string {

            $isChecked = filter_var($status, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            if (null === $isChecked) {
                $isChecked = ((string)$status) === '1';
            }

            return sprintf(
                "<p class='p_switch'><b>%s</b> <span class='switch'> <input name='%s' type='checkbox' id='%s' value='1' %s> <label for='%s' data-on='%s' data-off='%s'></label> </span></p>",
                $legend ?? '',
                $name,
                $name,
                $isChecked ? 'checked' : '',
                $name,
                $on,
                $off
            );
        }

        /**
         * Renderiza um botão (link) utilizado nos cartões.
         */
        public static function cardButton(string $link, string $title, string $icon, string $text): string
        {

            return "<a class='carduser_btn' href='" . htmlspecialchars(
                    $link,
                    ENT_QUOTES
                ) . "' target='_blank' title='" . htmlspecialchars(
                    $title,
                    ENT_QUOTES
                ) . "'><i class='" . htmlspecialchars(
                    $icon,
                    ENT_QUOTES
                ) . "'></i>" . htmlspecialchars($text, ENT_QUOTES) . '</a>';
        }

        public static function vCard(string $nome, string $telefone, string $email, string $cargo): void
        {

            // Definir os dados do contato
            $nome = Check::getCapilalize($nome);
            $telefone = Check::clearNumber($telefone);
            $email = mb_convert_case($email, MB_CASE_LOWER);
            $empresa = SITE_ADDR_NAME;
            $titulo = Check::getCapilalize($cargo);

            // Criar o conteúdo do vCard
            $vcfContent = 'BEGIN:VCARD' . PHP_EOL;
            $vcfContent .= 'VERSION:3.0' . PHP_EOL;
            $vcfContent .= 'FN:' . $nome . PHP_EOL;
            $vcfContent .= 'TEL;TYPE=CELL:' . $telefone . PHP_EOL;
            $vcfContent .= 'email:' . $email . PHP_EOL;
            $vcfContent .= 'ORG:' . $empresa . PHP_EOL;
            $vcfContent .= 'TITLE:' . $titulo . PHP_EOL;
            $vcfContent .= 'END:VCARD' . PHP_EOL;

            // Definir cabeçalhos HTTP para forçar o download do arquivo .vcf
            $fileName = Check::name($nome);
            header('Content-Type: text/vcard');
            header('Content-Disposition: attachment; filename="' . $fileName . '.vcf"');
            header('Content-Length: ' . strlen($vcfContent));

            // Enviar o conteúdo do vCard
            echo $vcfContent;

            exit;
        }

        public static function getCapilalize(string $str): string
        {

            $str = ucwords(mb_strtolower($str));

            return str_replace([
                ' De ',
                ' Da ',
                ' Do ',
                ' Das ',
                ' Dos ',
                ' Em ',
                ' As ',
                ' Os ',
            ], [
                ' de ',
                ' da ',
                ' do ',
                ' das ',
                ' dos ',
                ' em ',
                ' as ',
                ' os ',
            ], $str);
        }

        public static function clearNumber(string $phone_number): string
        {

            $digits = preg_replace('/\D+/', '', $phone_number);
            if (null === $digits) {
                $digits = '';
            }

            $digits = '55' . $digits;

            return trim(str_replace([' ', '(', ')', '-'], '', $digits));
        }

        /**
         * Transforma uma string em um slug amigável para URLs.
         */
        public static function name(string $name): string
        {

            $sanitized = trim(strip_tags($name));
            if ($sanitized === '') {
                return '';
            }
            $transliterated = $sanitized;

            if (class_exists(Normalizer::class)) {
                $normalized = Normalizer::normalize($transliterated, Normalizer::FORM_D);
                if (false !== $normalized) {
                    $withoutMarks = preg_replace('/\p{Mn}+/u', '', $normalized);
                    if (null !== $withoutMarks) {
                        $transliterated = $withoutMarks;
                    }
                }
            }

            if ($transliterated === $sanitized && function_exists('iconv')) {
                $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $sanitized);
                if (false !== $converted) {
                    $transliterated = $converted;
                }
            }

            $slug = preg_replace('/[^A-Za-z0-9]+/u', '-', $transliterated);
            if (null === $slug) {
                return '';
            }

            $slug = trim($slug, '-');

            if ($slug === '') {
                return '';
            }

            return mb_strtolower($slug, 'UTF-8');
        }

        public static function getMailUser(string $email): string
        {

            $parts = explode('@', $email);

            return $parts[0];
        }

        /**
         * Descreve nivel de usuário.
         */
        public static function getWcLevel(?int $level = null): mixed
        {

            $userLevel = [
                1 => 'Cliente (user)',
                2 => 'SAC (user)',
                6 => 'Marketing - Blog (adm)',
                7 => 'Suporte Geral (adm)',
                8 => 'Gerente Geral (adm)',
                9 => 'Administrador (adm)',
                10 => 'Super Admin (adm)'
            ];

            if (is_int($level) && array_key_exists($level, $userLevel) === true) {
                return $userLevel[$level];
            } else {
                return $userLevel;
            }
        }

        // Functions do Config/Config.inc.php

        /**
         * Exibe erros lançados por ajax.
         */
        public static function ajaxErro(string $errMsg, ?int $errNo = null): string
        {

            $cssClass = match ($errNo) {
                E_USER_NOTICE => 'trigger_info',
                E_USER_WARNING => 'trigger_alert',
                E_USER_ERROR => 'trigger_error',
                default => 'trigger_success',
            };

            return sprintf(
                "<div class='trigger trigger_ajax %s'><span>%s</span></div>",
                $cssClass,
                $errMsg
            );
        }

        public static function erro(string $errMsg, ?int $errNo = null): string
        {

            $cssClass = match ($errNo) {
                E_USER_NOTICE => 'trigger_info',
                E_USER_WARNING => 'trigger_alert',
                E_USER_ERROR => 'trigger_error',
                default => 'trigger_success',
            };

            return sprintf(
                "<div class='trigger %s'><span>%s</span></div>",
                $cssClass,
                $errMsg
            );
        }

        /**
         * personaliza o gatilho do PHP.
         */
        public static function phpErro(int $errNo, string $errMsg, string $errFile, int $errLine): void
        {

            echo "<div class='trigger trigger_error'>";
            echo sprintf('<b>Erro na Linha: #%s ::</b> %s<br>', $errLine, $errMsg);
            echo sprintf('<small>%s</small>', $errFile);
            echo "<span class='ajax_close'></span></div>";

            if (E_USER_ERROR === $errNo) {
                exit;
            }
        }

        public static function getWcMatLevels(?int $levels = null): array
        {

            $matLevels = [
                1 => 'Introdutório',
                2 => 'Intermediário',
                3 => 'Avançado'
            ];

            if (is_int($levels) && array_key_exists($levels, $matLevels) === true) {
                return $matLevels[$levels];
            } else {
                return $matLevels;
            }
        }

        public static function getWcMonths(?int $months = null): mixed
        {

            $postMonths = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro'
            ];

            if (is_int($months) && array_key_exists($months, $postMonths) === true) {
                return $postMonths[$months];
            } else {
                return $postMonths;
            }
        }

        /**
         * Converte comprimento de milímetros para polegadas.
         *
         * @param null|float|int $length comprimento em milímetros
         *
         * @return null|float comprimento em polegadas ou null se não informado
         */
        public static function getWcInches(float|int|null $length): ?float
        {

            if (null === $length) {
                return null;
            }

            return $length / 25.4;
        }

        public static function safeHtmlChars(?string $value): string
        {

            return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        }


        /**
         * Popula Select Cargos
         *
         * @param string|null $cargo
         *
         * @return string[]
         */
        public static function leadCargo(?string $cargo = null): mixed
        {

            $cargos = [
                'COMPRADOR' => 'COMPRADOR',
                'SUPERVISOR DE COMPRAS' => 'SUPERVISOR DE COMPRAS',
                'ENGENHARIA' => 'ENGENHARIA',
                'SUPERVISOR DE ENGENHARIA' => 'SUPERVISOR DE ENGENHARIA',
                'MANUTENÇÃO' => 'MANUTENÇÃO',
                'SUPERVISOR DE MANUTENÇÃO' => 'SUPERVISOR DE MANUTENÇÃO',
                'LOGÍSTICA' => 'LOGÍSTICA',
                'OUTRO' => 'OUTRO'
            ];


            return is_string($cargo) && array_find_key($cargos, $cargo) ? $cargos[$cargo] : $cargos;
        }


        /**
         * Popula Select Segmentos
         *
         * @param string|null $segmento
         *
         * @return string[]
         */
        public static function leadSegmento(?string $segmento = null): mixed
        {

            $segmentos = [
                'Indústria' => 'Indústria',
                'E-commerce' => 'E-commerce',
                'SaaS' => 'SaaS',
                'Saúde' => 'Saúde',
                'Educação' => 'Educação',
                'Outro' => 'Outro'
            ];

            return is_string($segmento) && array_find_key($segmentos, $segmento) ? $segmentos[$segmento] : $segmentos;
        }

        public static function accessBlocked(): mixed
        {

            return die('<div class="access-blocked"><b class="icon-blocked">ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
        }
    }
