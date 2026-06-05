<?php

    declare(strict_types=1);

    namespace App\Config;

    use App\Bootstrap\ErrorHandler;
    use App\Conn\Read;
    use Closure;
    use Dotenv\Dotenv;
    use RuntimeException;

    final class ConfigLoader
    {
        private static bool $booted = false;

        public static function boot(): void
        {

            if (self::$booted) {
                return;
            }

            self::$booted = true;

            self::loadEnv();
            self::validateRequiredEnv();
            self::defineDatabaseTableConstants();
            self::defineCacheConfig();
            self::defineDatabaseCredentials();
            self::loadConfigFromDatabase();
            self::defineAgencyConstants();
            self::defineClientConstants();
            self::defineBasePaths();
            self::defineAdminDefaults();
            self::defineMediaDefaults();
            self::defineApplicationModules();
            self::defineLevelPermissions();
            self::defineSegmentationDefaults();
            self::defineLinkAndAccountConfig();
            self::configureErrorHandling();
        }

        private static function loadEnv(): void
        {

            $envDir = __DIR__;

            if (file_exists($envDir . '/.env')) {
                $dotenv = Dotenv::createImmutable($envDir);
                $dotenv->safeLoad();
            }
        }

        private static function defineAgencyConstants(): void
        {

            /** @var array<string, \Closure|scalar> $agency */
            $agency = require __DIR__ . '/Agency.inc.php';
            self::defineConstants(self::withEnvOverrides($agency));
        }

        /**
         * @param array<string, \Closure|scalar> $constants
         */
        private static function defineConstants(array $constants): void
        {

            foreach ($constants as $name => $value) {
                self::defineIfNotDefined($name, $value);
            }
        }

        /**
         * @param null|\Closure|scalar $value
         */
        private static function defineIfNotDefined(string $name, $value): void
        {

            if (defined($name)) {
                return;
            }

            if ($value instanceof Closure) {
                $value = $value();
            }

            define($name, $value);
        }

        private static function defineClientConstants(): void
        {

            /** @var array<string, \Closure|scalar> $client */
            $client = require __DIR__ . '/Client.inc.php';
            self::defineConstants(self::withEnvOverrides($client));
        }

        private static function defineBasePaths(): void
        {

            $host = $_SERVER['HTTP_HOST'] ?? null;
            $isLocal = (is_string($host) && 'localhost' === $host);

            self::defineIfNotDefined(
                'BASE',
                self::env('APP_URL', ($isLocal ? 'https://localhost/zen' : 'https://zen.ppg.br'))
            );

            if (!defined('THEME')) {
                $sessionTheme = $_SESSION['WC_THEME'] ?? null;
                //$theme = is_string($sessionTheme) && '' !== $sessionTheme ? $sessionTheme : 'industing';
                $theme = self::env(
                    'APP_THEME',
                    is_string($sessionTheme) && '' !== $sessionTheme ? $sessionTheme : 'zen'
                );
                define('THEME', $theme);
            }

            self::defineIfNotDefined('INCLUDE_PATH', BASE . '/themes/' . THEME);
            self::defineIfNotDefined('REQUIRE_PATH', 'themes/' . THEME);
        }

        private static function defineAdminDefaults(): void
        {

            $defaults = [
                'ADMIN_NAME' => self::resolveEnvValue('ADMIN_NAME', 'ZENCONTROL'),
                'ADMIN_DESC' => self::resolveEnvValue(
                    'ADMIN_DESC',
                    'Sistema de gerenciamento de conteúdo e marketing digital da Zen Agência Web.'
                ),
                'ADMIN_MODE' => self::resolveEnvValue('ADMIN_MODE', 1),
                'ADMIN_WC_CUSTOM' => self::resolveEnvValue('ADMIN_WC_CUSTOM', 1),
                'ADMIN_MAINTENANCE' => self::resolveEnvValue('ADMIN_MAINTENANCE', 0),
                'ADMIN_VERSION' => self::resolveEnvValue('ADMIN_VERSION', '3.5.0'),
                'MAIL_HOST' => self::resolveEnvValue('MAIL_HOST', 'mail.seu-dominio.com.br'),
                'MAIL_PORT' => self::resolveEnvValue('MAIL_PORT', '465'), // 587 sem autenticação ou 465 autenticado
                'MAIL_USER' => self::resolveEnvValue('MAIL_USER', 'noreply@seu-dominio.com.br'),
                'MAIL_SMTP' => self::resolveEnvValue('MAIL_SMTP', 'noreply@seu-dominio.com.br'),
                'MAIL_PASS' => self::resolveEnvValue('MAIL_PASS', 'senha-do-email'),
                'MAIL_SENDER' => self::resolveEnvValue('MAIL_SENDER', 'Nome-do-remetente'),
                'MAIL_MODE' => self::resolveEnvValue(
                    'MAIL_MODE',
                    self::resolveEnvValue('MAIL_ENCRYPTION', 'tls')
                ),
                'MAIL_TESTER' => self::resolveEnvValue('MAIL_TESTER', 'e-mail-para-testes@xmail.com'),
            ];

            self::defineConstants($defaults);
        }

        private static function defineMediaDefaults(): void
        {

            $defaults = [
                'IMAGE_W' => 1200,
                'IMAGE_H' => 628,
                'THUMB_W' => 800,
                'THUMB_H' => 800,
                'AVATAR_W' => 500,
                'AVATAR_H' => 500,
                'SLIDE_W' => 1920,
                'SLIDE_H' => 1080,
                'VIDEO_W' => 1280,
                'VIDEO_H' => 720,
            ];

            self::defineConstants(self::withEnvOverrides($defaults));
        }

        private static function defineApplicationModules(): void
        {

            $defaults = [
                'APP_POSTS' => 1,
                'APP_POSTS_AMP' => 0,
                'APP_POSTS_INSTANT_ARTICLE' => 0,
                'APP_SEARCH' => 1,
                'APP_PAGES' => 1,
                'APP_COMMENTS' => 1,
                'APP_PORTFOLIO' => 0,
                'APP_SLIDE' => 1,
                'APP_USERS' => 1,
                'APP_VIDEOS' => 0,
                'APP_MATERIALS' => 0,
                'APP_DEPOSITIONS' => 0,
                'APP_ALBUMS' => 0,
                'APP_CURIOSITIES' => 0,
                'APP_CV' => 0,
                'APP_OUVIDORIA' => 0,
                'APP_CERTIFICATIONS' => 0,
                'APP_HELLO' => 0,
                'APP_LANDING_PAGES' => 1,
                'APP_THANKYOU_PAGES' => 1,
                'APP_LEADS' => 1,
                'APP_CTAS' => 1,
                'APP_PARTNERS' => 0,
                'APP_REPRESENTATIVES' => 0,
                'APP_LINKTREE' => 0,
                'APP_PRODUCTS_DORIPEL' => 1,
                'APP_DEBUG' => self::env('APP_DEBUG')
            ];

            self::defineConstants(self::withEnvOverrides($defaults));
        }

        private static function defineLevelPermissions(): void
        {

            $levels = [
                'LEVEL_WC_POSTS' => 6,
                'LEVEL_WC_COMMENTS' => 6,
                'LEVEL_WC_LINKTREE' => 6,
                'LEVEL_WC_CTAS' => 6,
                'LEVEL_WC_PORTFOLIO' => 6,
                'LEVEL_WC_PAGES' => 9,
                'LEVEL_WC_SLIDES' => 9,
                'LEVEL_WC_REPORTS' => 9,
                'LEVEL_WC_USERS' => 9,
                'LEVEL_WC_VIDEOS' => 9,
                'LEVEL_WC_DEPOSITIONS' => 9,
                'LEVEL_WC_PARTNERS' => 9,
                'LEVEL_WC_ALBUMS' => 9,
                'LEVEL_WC_PRODUCTS_DORIPEL' => 9,
                'LEVEL_WC_SERVICES' => 9,
                'LEVEL_WC_SEGMENTS' => 9,
                'LEVEL_WC_CURIOSITIES' => 9,
                'LEVEL_WC_CV' => 9,
                'LEVEL_WC_OUVIDORIA' => 9,
                'LEVEL_WC_CERTIFICATIONS' => 9,
                'LEVEL_WC_REPRESENTATIVES' => 9,
                'LEVEL_WC_HELLO' => 9,
                'LEVEL_WC_LANDING_PAGES' => 9,
                'LEVEL_WC_THANKYOU_PAGES' => 9,
                'LEVEL_WC_LEADS' => 9,
                'LEVEL_WC_CONFIG_MASTER' => 10,
                'LEVEL_WC_CONFIG_API' => 10,
                'LEVEL_WC_CONFIG_CODES' => 10,
            ];

            self::defineConstants(self::withEnvOverrides($levels));
        }

        private static function defineSegmentationDefaults(): void
        {

            $defaults = [
                'SEGMENT_FB_PAGE_ID' => '',
                'SEGMENT_FB_PIXEL_ID' => '',
                'SEGMENT_WC_USER' => 1,
                'SEGMENT_WC_BLOG' => 1,
                'SEGMENT_GL_ANALYTICS' => '',
                'SEGMENT_GL_TAGMANAGER' => '',
                'SEGMENT_GL_ADWORDS_ID' => '',
                'SEGMENT_GL_ADWORDS_LABEL' => ''
            ];

            self::defineConstants(self::withEnvOverrides($defaults));
        }

        private static function defineLinkAndAccountConfig(): void
        {

            $defaults = [
                'APP_LINK_POSTS' => 1,
                'APP_LINK_PAGES' => 1,
                'APP_LINK_PRODUCTS' => 1,
                'APP_LINK_PROPERTIES' => 1,
                'ACC_MANAGER' => 1,
                'E_PDT_SIZE' => 'default',
                'ACC_TAG' => 'Minha Conta',
                'COMMENT_MODERATE' => 1,
                'COMMENT_ON_POSTS' => 1,
                'COMMENT_ON_PAGES' => 1,
                'COMMENT_ON_PRODUCTS' => 1,
                'COMMENT_SEND_EMAIL' => 1,
                'COMMENT_ORDER' => 'DESC',
                'COMMENT_RESPONSE_ORDER' => 'ASC'
            ];

            self::defineConstants(self::withEnvOverrides($defaults));
        }

        private static function defineCacheConfig(): void
        {

            self::defineIfNotDefined('SIS_CACHE_TIME', self::resolveEnvValue('SIS_CACHE_TIME', 10));
            self::defineIfNotDefined('SIS_CONFIG_WC', self::resolveEnvValue('SIS_CONFIG_WC', 1));
            self::defineIfNotDefined('DB_AUTO_TRASH', self::resolveEnvValue('DB_AUTO_TRASH', 1));
            self::defineIfNotDefined('DB_AUTO_PING', self::resolveEnvValue('DB_AUTO_PING', 1));
        }

        private static function defineDatabaseTableConstants(): void
        {

            $tables = [
                'DB_CONF' => 'ws_config',
                'DB_USERS' => 'ws_users',
                'DB_USERS_ADDR' => 'ws_users_address',
                'DB_USERS_NOTES' => 'ws_users_notes',
                'DB_POSTS' => 'ws_posts',
                'DB_POSTS_IMAGE' => 'ws_posts_images',
                'DB_CATEGORIES' => 'ws_posts_categories',
                'DB_SEARCH' => 'ws_search',
                'DB_PAGES' => 'ws_pages',
                'DB_PAGES_IMAGE' => 'ws_pages_images',
                'DB_COMMENTS' => 'ws_comments',
                'DB_COMMENTS_LIKES' => 'ws_comments_likes',
                'DB_ORDERS_ITEMS' => 'ws_orders_items',
                'DB_HELLO' => 'ws_hellobar',
                'DB_SLIDES' => 'ws_slides',
                'DB_VIEWS_VIEWS' => 'ws_siteviews_views',
                'DB_VIEWS_ONLINE' => 'ws_siteviews_online',
                'DB_WC_API' => 'workcontrol_api',
                'DB_WC_CODE' => 'workcontrol_code',
                'DB_YOUTUBE' => 'ws_youtube',
                'DB_MATERIAIS' => 'ws_materiais',
                'DB_MATCATEGORIES' => 'ws_matcategories',
                'DB_LEADS' => 'ws_leads',
                'DB_ALBUMS' => 'ws_albums',
                'DB_ALBUMS_IMAGE' => 'ws_albums_images',
                'DB_DEPOSITIONS' => 'ws_depositions',
                'DB_PARTNERS' => 'ws_partners',
                'DB_LANDING_PAGES' => 'ws_landingpages',
                'DB_LANDING_PAGES_IMAGES' => 'ws_landingpages_images',
                'DB_THANKYOU_PAGES' => 'ws_thankyoupages',

                'DB_PDT_DORIPEL' => 'ws_products_doripel',
                'DB_PDT_IMAGE_DORIPEL' => 'ws_products_images_doripel',
                'DB_PDT_IMAGE_CAT_DORIPEL' => 'ws_products_images_cat_doripel',
                'DB_PDT_GALLERY_DORIPEL' => 'ws_products_gallery_doripel',
                'DB_PDT_CATS_DORIPEL' => 'ws_products_categories_doripel',
                'DB_PDT_BRANDS_DORIPEL' => 'ws_products_brands_doripel',
                'DB_PDT_COLORS_DORIPEL' => 'ws_products_colors_doripel',
                'DB_PDT_STOCK_DORIPEL' => 'ws_products_stock_doripel',

                'DB_REPRESENTATIVES' => 'ws_representatives',
                'DB_STATES' => 'states',
                'DB_CITIES' => 'cities',
                'DB_CURIOSITIES' => 'ws_curiosities',
                'DB_CV' => 'ws_job_candidates',
                'DB_OUVIDORIA' => 'ws_ouvidoria',
                'DB_CARD_USER' => 'trv_card_user',
                'DB_PORTFOLIO' => 'portfolio',
                'DB_PORTFOLIO_CATEGORIES' => 'portfolio_categories'
            ];

            self::defineConstants(self::withEnvOverrides($tables));
        }

        private static function defineDatabaseCredentials(): void
        {

            $isLocal = isset($_SERVER['HTTP_HOST']) && 'localhost' === $_SERVER['HTTP_HOST'];

            if ($isLocal) {
                self::defineIfNotDefined('SIS_DB_HOST', self::env('DB_HOST_DEV', 'localhost'));
                self::defineIfNotDefined('SIS_DB_USER', self::env('DB_USER_DEV', 'root'));
                self::defineIfNotDefined('SIS_DB_PASS', self::env('DB_PASSWORD_DEV', ''));
                self::defineIfNotDefined('SIS_DB_NAME', self::env('DB_NAME_DEV', ''));
            } else {
                self::defineIfNotDefined('SIS_DB_HOST', self::env('DB_HOST_PRODUCTION', 'localhost'));
                self::defineIfNotDefined('SIS_DB_USER', self::env('DB_USER_PRODUCTION', ''));
                self::defineIfNotDefined('SIS_DB_PASS', self::env('DB_PASSWORD_PRODUCTION', ''));
                self::defineIfNotDefined('SIS_DB_NAME', self::env('DB_NAME_PRODUCTION', ''));
            }
        }

        private static function env(string $key, mixed $default = null): mixed
        {

            return $_ENV[$key] ?? getenv($key) ?? $default;
        }

        private static function validateRequiredEnv(): void
        {

            $commonRequired = ['APP_ENV', 'APP_URL', 'APP_THEME'];
            $dbRequired = ['DB_HOST_DEV', 'DB_NAME_DEV', 'DB_USER_DEV'];
            $mailRequired = ['MAIL_HOST', 'MAIL_PORT', 'MAIL_USER', 'MAIL_SMTP', 'MAIL_SENDER', 'MAIL_MODE'];

            $requiredKeys = array_merge($commonRequired, $dbRequired, $mailRequired);
            if (self::isProductionEnvironment()) {
                $requiredKeys = array_merge(
                    $requiredKeys,
                    [
                        'DB_HOST_PRODUCTION',
                        'DB_NAME_PRODUCTION',
                        'DB_USER_PRODUCTION',
                        'DB_PASSWORD_PRODUCTION',
                        'MAIL_PASS'
                    ]
                );
            }

            $missingKeys = self::missingEnvKeys($requiredKeys);
            if ([] === $missingKeys) {
                return;
            }

            $message = 'Variáveis obrigatórias ausentes no .env: ' . implode(', ', $missingKeys);
            if (self::isProductionEnvironment()) {
                throw new RuntimeException($message);
            }

            trigger_error($message, E_USER_WARNING);
        }

        /**
         * @param array<int, string> $keys
         *
         * @return array<int, string>
         */
        private static function missingEnvKeys(array $keys): array
        {

            $missing = [];
            foreach ($keys as $key) {
                $value = self::env($key, null);
                if (!is_scalar($value) || '' === trim((string)$value)) {
                    $missing[] = $key;
                }
            }

            return $missing;
        }

        private static function isProductionEnvironment(): bool
        {

            $appEnv = strtolower((string)self::env('APP_ENV', 'local'));

            return in_array($appEnv, ['production', 'prod'], true);
        }

        /**
         * @param array<string, \Closure|scalar> $constants
         *
         * @return array<string, mixed>
         */
        private static function withEnvOverrides(array $constants): array
        {

            foreach ($constants as $name => $value) {
                if ($value instanceof Closure) {
                    $value = $value();
                }

                $constants[$name] = self::resolveEnvValue($name, $value);
            }

            return $constants;
        }

        private static function resolveEnvValue(string $name, mixed $default): mixed
        {

            $value = self::env($name, null);
            if (null === $value) {
                return $default;
            }

            if (is_bool($default)) {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
            }

            if (is_int($default)) {
                return is_numeric($value) ? (int)$value : $default;
            }

            if (is_float($default)) {
                return is_numeric($value) ? (float)$value : $default;
            }

            if (
                is_scalar($value) && '' === trim((string)$value) && is_scalar($default) && '' !== trim(
                    (string)$default
                )
            ) {
                return $default;
            }

            return is_scalar($value) ? (string)$value : $default;
        }

        private static function loadConfigFromDatabase(): void
        {

            $sisConfig = (int)(defined('SIS_CONFIG_WC') ? constant('SIS_CONFIG_WC') : 0);
            $isCli = PHP_SAPI === 'cli' || PHP_SAPI === 'cli-server';
            $disableDb = '1' === getenv('DISABLE_DB_ON_BOOT');

            if (0 === $sisConfig || $isCli || $disableDb) {
                return;
            }

            if (!defined('DB_CONF') || '' === trim((string)constant('DB_CONF'))) {
                return;
            }

            $read = new Read();
            $read->fullRead('SELECT conf_key, conf_value FROM ' . DB_CONF);
            $configs = $read->getResult();
            if (!is_array($configs) || [] === $configs) {
                return;
            }

            foreach ($configs as $configRow) {
                if (!is_array($configRow)) {
                    continue;
                }

                $constantName = self::stringValue($configRow['conf_key'] ?? null);
                if (null === $constantName || !self::isDatabaseOverridableConstant($constantName)) {
                    continue;
                }

                if (
                    'THEME' === $constantName
                    && isset($_SESSION['WC_THEME'])
                    && '' !== $_SESSION['WC_THEME']
                ) {
                    continue;
                }

                if (self::hasEnvValue($constantName) || defined($constantName)) {
                    continue;
                }

                $value = $configRow['conf_value'] ?? null;
                if (null === $value) {
                    define($constantName, '');

                    continue;
                }

                $resolvedValue = self::resolveDatabaseConstantValue($constantName, $value);
                if (is_scalar($resolvedValue)) {
                    define($constantName, $resolvedValue);
                }
            }
        }

        private static function resolveDatabaseConstantValue(string $constantName, mixed $value): mixed
        {

            if (!is_scalar($value)) {
                return null;
            }

            $raw = trim((string)$value);
            if (self::isIntegerLikeConstant($constantName)) {
                $booleanInt = self::booleanLikeToInt($raw);
                if (null !== $booleanInt) {
                    return $booleanInt;
                }

                if (is_numeric($raw)) {
                    return (int)$raw;
                }
            }

            return (string)$value;
        }

        private static function isIntegerLikeConstant(string $constantName): bool
        {

            if (
                in_array(
                    $constantName,
                    ['ADMIN_MODE', 'ADMIN_WC_CUSTOM', 'ADMIN_MAINTENANCE', 'SITE_SOCIAL_FB'],
                    true
                )
            ) {
                return true;
            }

            if (in_array($constantName, ['SEGMENT_WC_USER', 'SEGMENT_WC_BLOG'], true)) {
                return true;
            }

            $integerPrefixes = [
                'APP_',
                'COMMENT_',
                'ACC_',
                'LEVEL_WC_',
                'IMAGE_',
                'THUMB_',
                'AVATAR_',
                'SLIDE_',
                'VIDEO_'
            ];
            foreach ($integerPrefixes as $prefix) {
                if (str_starts_with($constantName, $prefix)) {
                    return true;
                }
            }

            return false;
        }

        private static function booleanLikeToInt(string $value): ?int
        {

            if ('' === $value) {
                return null;
            }

            $normalized = strtolower($value);
            if (in_array($normalized, ['1', 'true', 'on', 'yes', 'sim'], true)) {
                return 1;
            }

            if (in_array($normalized, ['0', 'false', 'off', 'no', 'nao', 'não'], true)) {
                return 0;
            }

            return null;
        }

        private static function isDatabaseOverridableConstant(string $constantName): bool
        {

            if ('THEME' === $constantName) {
                return true;
            }

            $allowedPrefixes = [
                'APP_',
                'COMMENT_',
                'ACC_',
                'SEGMENT_',
                'IMAGE_',
                'THUMB_',
                'AVATAR_',
                'SLIDE_',
                'VIDEO_',
                'SITE_',
                'AGENCY_',
                'LEVEL_WC_',
                'ADMIN_',
            ];

            foreach ($allowedPrefixes as $prefix) {
                if (str_starts_with($constantName, $prefix)) {
                    return true;
                }
            }

            return false;
        }

        private static function hasEnvValue(string $key): bool
        {

            $value = self::env($key, null);

            return is_scalar($value) && '' !== trim((string)$value);
        }

        private static function stringValue(mixed $value): ?string
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

        private static function configureErrorHandling(): void
        {

            if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') {
                ErrorHandler::register(self::shouldRenderJsonErrors());
            } else {
                $currentReporting = error_reporting();
                error_reporting($currentReporting & ~E_DEPRECATED & ~E_USER_DEPRECATED);
            }
        }

        /**
         * Identifica contextos AJAX/API para resposta de erro em JSON.
         */
        private static function shouldRenderJsonErrors(): bool
        {

            $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '');
            $accept = (string)($_SERVER['HTTP_ACCEPT'] ?? '');
            $requestedWith = (string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
            $contentType = (string)($_SERVER['CONTENT_TYPE'] ?? '');

            if ('' !== $requestUri && (str_contains($requestUri, '/ajax/') || str_contains($requestUri, '.ajax.php'))) {
                return true;
            }

            if ('' !== $requestedWith && 'xmlhttprequest' === strtolower($requestedWith)) {
                return true;
            }

            if ('' !== $accept && str_contains(strtolower($accept), 'application/json')) {
                return true;
            }

            if ('' !== $contentType && str_contains(strtolower($contentType), 'application/json')) {
                return true;
            }

            return false;
        }

    }
