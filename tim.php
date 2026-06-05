<?php

    declare(strict_types=1);

    /*ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);*/

    require_once __DIR__ . '/vendor/autoload.php';

    use League\Flysystem\Filesystem;
    use League\Flysystem\Local\LocalFilesystemAdapter;
    use League\Flysystem\MountManager;
    use League\Glide\ServerFactory;

// ------------------------------------------------------------
// 1) ORIGENS
// ------------------------------------------------------------
    $roots = [
        'uploads' => __DIR__ . '/uploads',
        'themes' => __DIR__ . '/themes/' . THEME . '/images',
        'admin' => __DIR__ . '/admin/_img',
    ];

// ------------------------------------------------------------
// 2) CACHE LOCAL
// ------------------------------------------------------------
    $cacheRoot = __DIR__ . '/cache';
    if (!is_dir($cacheRoot)) {
        mkdir($cacheRoot, 0775, true);
    }

// ------------------------------------------------------------
// 3) FILESYSTEMS + MOUNT MANAGER
// ------------------------------------------------------------
    $filesystems = [];
    foreach ($roots as $prefix => $path) {
        $filesystems[$prefix] = new Filesystem(new LocalFilesystemAdapter($path));
    }
    $source = new MountManager($filesystems);
    $cache = new Filesystem(new LocalFilesystemAdapter($cacheRoot));

// ------------------------------------------------------------
// 4) ENTRADAS
// ------------------------------------------------------------
    $rawSrc = (string)(filter_input(INPUT_GET, 'src', FILTER_SANITIZE_URL) ?? '');
    $width = filter_input(INPUT_GET, 'w', FILTER_VALIDATE_INT) ?: null;
    $height = filter_input(INPUT_GET, 'h', FILTER_VALIDATE_INT) ?: null;
    $fit = filter_input(INPUT_GET, 'fit', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: 'crop';

// Sanitização
    $rawSrc = str_replace(['..\\', '../', '\..'], '', $rawSrc);
    $rawSrc = ltrim($rawSrc, '/');

// URL absoluta? pega só o path
    if (preg_match('#^https?://#i', $rawSrc)) {
        $parts = parse_url($rawSrc);
        $rawSrc = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    }


    function resolvePrefixedPath(string $raw): ?array
    {

        // já no formato prefix://path ?
        if (preg_match('~^([a-z0-9_-]+)://(.+)$~i', $raw, $m)) {
            return [strtolower($m[1]) . '://', ltrim($m[2], '/')];
        }

        $low = strtolower($raw);
        if (str_starts_with($low, '/themes/' . THEME . '/images/')) {
            return ['themes://', substr($raw, strlen('/themes/' . THEME . '/images/'))];
        }
        if (str_starts_with($low, 'uploads/')) {
            return ['uploads://', substr($raw, strlen('uploads/'))];
        }
        if (str_starts_with($low, 'admin/_img/')) {
            return ['admin://', substr($raw, strlen('admin/_img/'))];
        }

        // fallback: tente uploads
        if ('' !== $raw) {
            return ['uploads://', $raw];
        }

        return null;
    }

    $resolved = resolvePrefixedPath($rawSrc);
    if (null === $resolved) {
        http_response_code(400);

        exit('Origem de imagem não suportada.');
    }
    [$prefixUri, $subpath] = $resolved;
    $prefixed = $prefixUri . ltrim($subpath, '/');

// ------------------------------------------------------------
// 6) EXTENSÃO / EXISTÊNCIA
// ------------------------------------------------------------
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $ext = strtolower(pathinfo($subpath, PATHINFO_EXTENSION));
    if ('' !== $ext && !in_array($ext, $allowedExt, true)) {
        http_response_code(400);

        exit('Extensão não permitida.');
    }

    if (!$source->fileExists($prefixed)) {
        http_response_code(404);

        exit('Imagem não encontrada.');
    }

// ------------------------------------------------------------
// 7) GLIDE SERVER
// ------------------------------------------------------------
    $server = ServerFactory::create([
        'source' => $source,   // MountManager (Flysystem v3)
        'cache' => $cache,    // Filesystem local
        'cache_path_prefix' => 'glide',
    ]);

    $params = array_filter([
        'w' => $width,
        'h' => $height,
        'fit' => $fit, // crop|max|fill|contain
    ], fn($v) => null !== $v);

// Entrega
    $server->outputImage($prefixed, $params);
