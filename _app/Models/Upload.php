<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Check;
use GdImage;

use function constant;
use function ctype_digit;
use function date;
use function defined;
use function explode;
use function file_exists;
use function imagealphablending;
use function imagecopyresampled;
use function imagecreatefromjpeg;
use function imagecreatefrompng;
use function imagecreatefromwebp;
use function imagecreatetruecolor;
use function imagegif;
use function imagejpeg;
use function imagepng;
use function imagesavealpha;
use function imagesx;
use function imagesy;
use function imagewebp;
use function implode;
use function in_array;
use function is_dir;
use function is_int;
use function is_numeric;
use function is_string;
use function max;
use function mb_strtolower;
use function mb_strtoupper;
use function min;
use function mkdir;
use function move_uploaded_file;
use function pathinfo;
use function round;
use function rtrim;
use function sprintf;
use function str_replace;
use function str_starts_with;
use function strrpos;
use function substr;
use function time;
use function trim;

/**
 * Upload [ Helper ]
 * Responsável por executar upload de imagens, arquivos e mídias no sistema.
 */
class Upload
{
    private static string $baseDir;

    /**
     * @var array<string, mixed>
     */
    private array $file = [];

    private string $name = '';

    private string $ext = '';

    private string $send = '';

    // IMAGE UPLOAD
    private int $width = 0;

    private ?GdImage $image = null;

    // RESULTSET
    private false|string|null $result = null;

    private ?string $error = null;

    // DIRETÓRIOS
    private string $folder = '';

    /**
     * Verifica e cria o diretório padrão de uploads no sistema!<br>
     * <b>../uploads/</b>.
     */
    public function __construct(?string $baseDir = null)
    {

        self::$baseDir = (null !== $baseDir && '' !== $baseDir && '0' !== $baseDir) ? $baseDir : '../uploads/';
        if (!is_dir(self::$baseDir)) {
            // Cria diretório base de forma recursiva (PSR: sem estruturas alternativas)
            @mkdir(self::$baseDir, 0777, true);
        }
    }

    /**
     * <b>Enviar Imagem:</b> Basta envelopar um $_FILES de uma imagem e caso queira um nome e uma largura personalizada.
     * Caso não informe a largura será 1024!
     *
     * @param array<string, mixed> $image Envelopa $_FILES (JPG, PNG, GIF, WEBP)
     * @param null|string $name Nome desejado do arquivo (sem extensão)
     * @param null|int $width Largura desejada (padrão IMAGE_W)
     * @param null|string $folder Pasta personalizada (ex: images)
     */
    public function image(array $image, ?string $name = null, ?int $width = null, ?string $folder = null): void
    {

        $this->file = $image;
        $filename = $this->getFileStringValue('name');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $this->ext = '' !== $ext ? ('.' . mb_strtolower($ext)) : '';

        if (null !== $name && '' !== $name && '0' !== $name) {
            $baseName = $name;
        } else {
            $position = strrpos($filename, '.');
            $baseName = ('' !== $filename && false !== $position) ? substr($filename, 0, $position) : $filename;
        }

        $this->name = mb_strtolower($baseName);

        $defaultWidth = $this->getDefaultImageWidth();
        $this->width = (null !== $width && $width > 0) ? $width : $defaultWidth;
        $this->folder = (null !== $folder && '' !== $folder && '0' !== $folder) ? $folder : 'images';

        $extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

        $mimeType = $this->getFileStringValue('type');

        if (
            !in_array($this->ext, $extensions, true) || '' === $mimeType || !str_starts_with(
                $mimeType,
                'image/'
            )
        ) {
            $this->result = false;
            $this->error = 'Tipo de imagem não aceito. Extensões aceitas: ' . mb_strtoupper(
                    str_replace('.', '', implode(', ', $extensions))
                ) . '!';

            return;
        }

        $this->checkFolder($this->folder);
        $this->setFileName();
        $this->uploadImage();
    }

    // PRIVATE METHODS

    // Verifica e cria os diretórios com base em tipo de arquivo, ano e mês!

    private function getFileStringValue(string $key): string
    {

        $value = $this->file[$key] ?? null;

        return is_string($value) ? $value : '';
    }

    // Verifica e monta o nome dos arquivos tratando a string!

    /**
     * <b>Enviar Mídia:</b> Basta envelopar um $_FILES de uma mídia e caso queira um nome e um tamanho personalizado.
     * Caso não informe o tamanho será 40mb!
     */

    private function getDefaultImageWidth(): int
    {

        $constantName = $this->imageWidthConstantName();
        if (defined($constantName)) {
            $value = constant($constantName);
            if (is_int($value)) {
                return $value;
            }

            if (is_numeric($value)) {
                return (int)$value;
            }
        }

        return 1024;
    }

    // Realiza o upload de imagens redimensionando a mesma!

    private function imageWidthConstantName(): string
    {

        return 'IMAGE_W';
    }

    // Envia arquivos e mídias

    private function checkFolder(string $folder): void
    {

        [$y, $m] = explode('/', date('Y/m'));

        $this->createFolder($folder);
        $this->createFolder($folder . '/' . $y);
        $this->createFolder($folder . '/' . $y . '/' . $m);

        $this->send = $folder . '/' . $y . '/' . $m . '/';
    }

    private function createFolder(string $folder): void
    {

        $path = rtrim(self::$baseDir, '/') . '/' . trim($folder, '/');
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }
    }

    private function setFileName(): void
    {

        $originalExt = $this->ext; // já com ponto
        $fileName = Check::name($this->name) . $originalExt;
        $target = rtrim(self::$baseDir, '/') . '/' . $this->send . $fileName;
        if (file_exists($target)) {
            $fileName = Check::name($this->name) . '-' . time() . $originalExt;
        }

        $this->name = mb_strtolower($fileName);
    }

    private function uploadImage(): void
    {

        $type = $this->getFileStringValue('type');
        $tmpName = $this->getFileStringValue('tmp_name');

        switch ($type) {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $resource = @imagecreatefromjpeg($tmpName);
                $this->image = $resource instanceof GdImage ? $resource : null;

                break;

            case 'image/png':
            case 'image/x-png':
                $resource = @imagecreatefrompng($tmpName);
                $this->image = $resource instanceof GdImage ? $resource : null;

                break;

            case 'image/gif':
                // GIF animado: não redimensionar para manter animação
                if (move_uploaded_file($tmpName, rtrim(self::$baseDir, '/') . '/' . $this->send . $this->name)) {
                    $this->result = $this->send . $this->name;
                    $this->error = null;
                } else {
                    $this->result = false;
                    $this->error = 'Erro ao mover o GIF animado!';
                }

                return;

            case 'image/webp':
                $resource = @imagecreatefromwebp($tmpName);
                $this->image = $resource instanceof GdImage ? $resource : null;

                break;

            default:
                $this->image = null;

                break;
        }

        if (!$this->image instanceof GdImage) {
            $this->result = false;
            $this->error = 'Tipo de arquivo inválido, envie imagens JPG, PNG, WEBP, GIF!';

            return;
        }

        $x = imagesx($this->image);
        $y = imagesy($this->image);
        $imageX = max(1, min($this->width, $x));
        $imageH = max(1, (int)round(($imageX * $y) / max($x, 1)));

        $newImageResource = @imagecreatetruecolor($imageX, $imageH);
        if (!$newImageResource instanceof GdImage) {
            $this->result = false;
            $this->error = 'Falha ao alocar recurso de imagem.';

            return;
        }

        $newImage = $newImageResource;
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $imageX, $imageH, $x, $y);

        $target = rtrim(self::$baseDir, '/') . '/' . $this->send . $this->name;

        switch ($type) {
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($newImage, $target);

                break;

            case 'image/png':
            case 'image/x-png':
                imagepng($newImage, $target);

                break;

            case 'image/gif':
                imagegif($newImage, $target);

                break;

            case 'image/webp':
                imagewebp($newImage, $target);

                break;
        }

        $this->result = $this->send . $this->name;
        $this->error = null;

        // Em PHP 8+, recursos GD são liberados automaticamente.
        $this->image = null;
        unset($newImage);
    }

    /**
     * <b>Enviar Arquivo:</b> Basta envelopar um $_FILES de um arquivo e caso queira um nome e um tamanho personalizado.
     * Caso não informe o tamanho será 200mb.
     *
     * @param array<string, mixed> $file Envelope $_FILES (PDF ou DOCX)
     * @param null|string $name Nome do arquivo (ou do artigo)
     * @param null|string $folder Pasta personalizada
     * @param null|int $maxFileSize Tamanho máximo do arquivo em MB (padrão 200)
     */
    public function file(array $file, ?string $name = null, ?string $folder = null, ?int $maxFileSize = null): void
    {

        $this->file = $file;
        $filename = $this->getFileStringValue('name');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $this->ext = '' !== $ext ? ('.' . mb_strtolower($ext)) : '';
        if (null !== $name && '' !== $name && '0' !== $name) {
            $baseName = $name;
        } else {
            $position = strrpos($filename, '.');
            $baseName = ('' !== $filename && false !== $position) ? substr($filename, 0, $position) : $filename;
        }

        $this->name = mb_strtolower($baseName);
        $this->folder = (null !== $folder && '' !== $folder && '0' !== $folder) ? $folder : 'files';
        $maxFileSize = (null !== $maxFileSize && $maxFileSize > 0) ? $maxFileSize : 200; // MB

        $FileAccept = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/pdf',
            'application/x-rar-compressed',
            'application/x-zip-compressed',
            'application/octet-stream',
            'application/zip',
            'text/xml',
        ];

        // VALID EXTENSION FOR FILES
        // $Extension = ['.pdf', '.doc', '.docx', '.rar', '.zip', '.xml'];
        $Extension = ['.pdf', '.doc', '.docx'];

        $mimeType = $this->getFileStringValue('type');

        if (
            !in_array($this->ext, $Extension, true) || !in_array(
                $mimeType,
                $FileAccept,
                true
            )
        ) {
            $this->result = false;
            $this->error = 'Tipo de arquivo não aceito. Extensões aceitas: ' . mb_strtoupper(
                    str_replace('.', '', implode(', ', $Extension))
                ) . '!';
        } elseif ($this->getFileSize() > ($maxFileSize * (1024 * 1024))) {
            $this->result = false;
            $this->error = sprintf('Arquivo muito grande, tamanho máximo permitido de %smb', $maxFileSize);
        } else {
            $this->checkFolder($this->folder);
            $this->setFileName();
            $this->moveFile();
        }
    }

    // Verifica e cria o diretório base!

    private function getFileSize(): int
    {

        $value = $this->file['size'] ?? null;

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && '' !== $value && ctype_digit($value)) {
            return (int)$value;
        }

        return 0;
    }

    private function moveFile(): void
    {

        $tmp = $this->getFileStringValue('tmp_name');
        $target = rtrim(self::$baseDir, '/') . '/' . $this->send . $this->name;
        if (move_uploaded_file($tmp, $target)) {
            $this->result = $this->send . $this->name;
            $this->error = null;
        } else {
            $this->result = false;
            $this->error = 'Erro ao mover o arquivo. Favor tente mais tarde!';
        }
    }

    /**
     * @param array<string, mixed> $media Envelope $_FILES (MP3 ou vídeos)
     * @param null|string $name Nome desejado do arquivo (sem extensão)
     * @param null|string $folder Pasta personalizada
     * @param null|int $maxFileSize Tamanho máximo permitido em MB (padrão 50)
     */
    public function media(array $media, ?string $name = null, ?string $folder = null, ?int $maxFileSize = null): void
    {

        $this->file = $media;
        $filename = $this->getFileStringValue('name');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $this->ext = '' !== $ext ? ('.' . mb_strtolower($ext)) : '';
        if (null !== $name && '' !== $name && '0' !== $name) {
            $baseName = $name;
        } else {
            $position = strrpos($filename, '.');
            $baseName = ('' !== $filename && false !== $position) ? substr($filename, 0, $position) : $filename;
        }

        $this->name = mb_strtolower($baseName);
        $this->folder = (null !== $folder && '' !== $folder && '0' !== $folder) ? $folder : 'medias';
        $maxFileSize = (null !== $maxFileSize && $maxFileSize > 0) ? $maxFileSize : 50; // MB

        $FileAccept = [
            'audio/mp3',
            'video/mp4',
            'video/webm',
        ];

        // VALID EXTENSIONS FOR MEDIAS
        $Extension = ['.mp3', '.mp4', '.webm'];

        if ($this->getFileSize() > ($maxFileSize * (1024 * 1024))) {
            $this->result = false;
            $this->error = sprintf('Arquivo muito grande, tamanho máximo permitido de %smb', $maxFileSize);
        } elseif (
            !in_array($this->ext, $Extension, true) || !in_array(
                $this->getFileStringValue('type'),
                $FileAccept,
                true
            )
        ) {
            $this->result = false;
            $this->error = 'Tipo de arquivo não aceito. Extensões aceitas: ' . mb_strtoupper(
                    str_replace('.', '', implode(', ', $Extension))
                ) . '!';
        } else {
            $this->checkFolder($this->folder);
            $this->setFileName();
            $this->moveFile();
        }
    }

    /**
     * <b>Verificar Upload:</b> Executando um getResult é possível verificar se o Upload foi executado ou não. Retorna
     * uma string com o caminho e nome do arquivo ou FALSE.
     * @return null|false|string Caminho e nome do arquivo ou false em caso de erro
     */
    public function getResult(): false|string|null
    {

        return $this->result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um code, um title, um erro e um tipo.
     * @return null|string Mensagem de erro ou null quando não há falhas
     */
    public function getError(): ?string
    {

        return $this->error;
    }
}
