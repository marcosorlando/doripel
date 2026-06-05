<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\Check;
use PHPMailer\PHPMailer\PHPMailer;

use function array_key_exists;
use function getenv;
use function in_array;
use function is_array;
use function is_bool;
use function is_numeric;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;
use function strip_tags;
use function strtolower;
use function trigger_error;
use function trim;

/**
 * Email [ MODEL ]
 * Modelo responsável por configurar a PHPMailer, validar os dados e disparar e-mails do sistema!
 * @copyright (c) year, Marcos Orlando - ZEN AGÊNCIA WEB
 */
class Email
{
    private const array LEGACY_PROPERTY_MAP = [
        'File' => 'file',
        'Mail' => 'mail',
        'Data' => 'data',
        'Assunto' => 'subject',
        'Mensagem' => 'message',
        'RemetenteNome' => 'senderName',
        'RemetenteEmail' => 'senderEmail',
        'DestinoNome' => 'recipientName',
        'DestinoEmail' => 'recipientEmail',
        'Error' => 'error',
        'Result' => 'result',
    ];

    public bool $file = false;

    public PHPMailer $mail;

    /**
     * @var array<string, null|string>
     */
    private array $data = [];

    private string $subject = '';

    private string $message = '';

    private ?string $senderName = null;

    private string $senderEmail = '';

    private ?string $recipientName = null;

    private string $recipientEmail = '';

    private ?string $error = null;

    private bool $result = false;

    public function __construct()
    {

        $this->mail = new PHPMailer();
        $this->mail->Host = MAIL_HOST;
        $this->mail->Port = $this->resolvePort();
        $this->mail->Username = MAIL_SMTP;
        $this->mail->Password = MAIL_PASS;
        $this->mail->SMTPAuth = true;

        $mailMode = $this->resolveSecureMode();
        if (null !== $mailMode) {
            $this->mail->SMTPSecure = $mailMode;
        }
    }

    private function resolvePort(): int
    {

        $portFromEnv = getenv('MAIL_PORT');
        $port = false === $portFromEnv ? MAIL_PORT : $portFromEnv;

        return (int)trim($port);
    }

    private function resolveSecureMode(): ?string
    {

        $modeFromEnv = getenv('MAIL_MODE');
        $mode = false === $modeFromEnv ? MAIL_MODE : $modeFromEnv;

        $normalized = strtolower(trim($mode));
        if ('' === $normalized || in_array($normalized, ['0', 'off', 'false', 'none'], true)) {
            return null;
        }

        return $normalized;
    }

    public function __get(string $name): mixed
    {

        if (array_key_exists($name, self::LEGACY_PROPERTY_MAP)) {
            $property = self::LEGACY_PROPERTY_MAP[$name];

            return $this->getLegacyProperty($property);
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);

        return null;
    }

    /**
     * <b>enviar E-mail SMTP:</b> Envelope os dados do e-mail em um array atribuitivo para povoar o método.
     * Com isso execute este para ter toda a validação de envio do e-mail feita automaticamente.
     * <b>REQUER DADOS ESPECÍFICOS:</b> Para enviar o e-mail você deve montar um array atribuitivo com os
     * seguintes índices corretamente povoados:<br><br>
     * <i>
     * &raquo; Assunto<br>
     * &raquo; Mensagem<br>
     * &raquo; RemetenteNome<br>
     * &raquo; RemetenteEmail<br>
     * &raquo; DestinoNome<br>
     * &raquo; DestinoEmail
     * </i>.
     */

    public function __set(string $name, mixed $value): void
    {

        if (array_key_exists($name, self::LEGACY_PROPERTY_MAP)) {
            $property = self::LEGACY_PROPERTY_MAP[$name];
            $this->setLegacyProperty($property, $value);

            return;
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
    }

    // configura o PHPMailer e valida o e-mail!

    private function getLegacyProperty(string $property): mixed
    {

        return match ($property) {
            'file' => $this->file,
            'mail' => $this->mail,
            'data' => $this->data,
            'subject' => $this->subject,
            'message' => $this->message,
            'senderName' => $this->senderName,
            'senderEmail' => $this->senderEmail,
            'recipientName' => $this->recipientName,
            'recipientEmail' => $this->recipientEmail,
            'error' => $this->error,
            'result' => $this->result,
            default => null,
        };
    }

    // Envia o e-mail!

    private function setLegacyProperty(string $property, mixed $value): void
    {

        switch ($property) {
            case 'file':
                $this->file = (bool)$value;

                return;

            case 'mail':
                if ($value instanceof PHPMailer) {
                    $this->mail = $value;
                }

                return;

            case 'data':
                if (is_array($value)) {
                    // @var array<string, mixed> $value
                    $this->data = $this->sanitizePayload($value);
                }

                return;

            case 'subject':
                $this->subject = $this->normalizeStringValue($value);

                return;

            case 'message':
                $this->message = $this->normalizeStringValue($value);

                return;

            case 'senderName':
                $this->senderName = $this->normalizeNullableField($value);

                return;

            case 'senderEmail':
                $this->senderEmail = $this->normalizeStringValue($value);

                return;

            case 'recipientName':
                $this->recipientName = $this->normalizeNullableField($value);

                return;

            case 'recipientEmail':
                $this->recipientEmail = $this->normalizeStringValue($value);

                return;

            case 'error':
                $this->error = null === $value ? null : $this->normalizeStringValue($value);

                return;

            case 'result':
                $this->result = (bool)$value;

                return;
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, null|string>
     */
    private function sanitizePayload(array $data): array
    {

        $allowedKeys = [
            'Assunto',
            'Mensagem',
            'RemetenteNome',
            'RemetenteEmail',
            'DestinoNome',
            'DestinoEmail',
        ];

        $sanitized = [];
        foreach ($allowedKeys as $key) {
            $value = $data[$key] ?? null;
            if (null === $value) {
                $sanitized[$key] = null;

                continue;
            }

            if (is_string($value) || is_numeric($value) || is_bool($value)) {
                $sanitized[$key] = (string)$value;

                continue;
            }

            if (is_object($value) && method_exists($value, '__toString')) {
                $sanitized[$key] = (string)$value;

                continue;
            }

            $sanitized[$key] = null;
        }

        return $sanitized;
    }

    private function normalizeStringValue(mixed $value): string
    {

        if (is_string($value)) {
            return $value;
        }

        if (null === $value) {
            return '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return (string)$value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string)$value;
        }

        return '';
    }

    private function normalizeNullableField(mixed $value): ?string
    {

        if (null === $value) {
            return null;
        }

        if (!is_string($value) && !is_numeric($value) && !is_bool($value)) {
            if (!is_object($value) || !method_exists($value, '__toString')) {
                return null;
            }

            $value = (string)$value;
        }

        $value = trim((string)$value);

        if ('' === $value || 'null' === strtolower($value)) {
            return null;
        }

        return strip_tags($value);
    }

    /**
     * <b>Montar e enviar:</b> Execute este método para facilitar o envio. Informando os parâmetros solicitados para
     * montar a data!
     */
    public function enviarMontando(
        mixed $Assunto,
        mixed $Mensagem,
        mixed $RemetenteNome,
        mixed $RemetenteEmail,
        mixed $DestinoNome,
        mixed $DestinoEmail
    ): void {

        $payload = [
            'Assunto' => $Assunto,
            'Mensagem' => $Mensagem,
            'RemetenteNome' => $RemetenteNome,
            'RemetenteEmail' => $RemetenteEmail,
            'DestinoNome' => $DestinoNome,
            'DestinoEmail' => $DestinoEmail,
        ];

        $this->enviar($payload);
    }

    /**
     * @param array<string, mixed> $Data
     */
    public function enviar(array $Data): void
    {

        $this->data = $this->sanitizePayload($Data);
        $this->clear();

        if (in_array('', $this->data, true)) {
            $this->error = '<b>ERRO AO enviar E-MAIL:</b> Dados informados são insuficientes para disparo de mensagem!';
            $this->result = false;
        } elseif (!Check::email((string)$this->data['RemetenteEmail'])) {
            $this->error = '<b>ERRO AO enviar E-MAIL:</b> O endereço de e-mail informado para o remetente não tem um formato válido!';
            $this->result = false;
        } else {
            $this->data['RemetenteNome'] = $this->normalizeNullableField($this->data['RemetenteNome'] ?? null);
            $this->data['DestinoNome'] = $this->normalizeNullableField($this->data['DestinoNome'] ?? null);
            $this->setMail();
            $this->config();
            $this->sendMail();
        }
    }

    private function clear(): void
    {

        foreach ($this->data as $key => $value) {
            if (!is_string($value)) {
                continue;
            }

            if ('Mensagem' === $key) {
                $this->data[$key] = trim($value);

                continue;
            }

            $this->data[$key] = trim(strip_tags($value));
        }
    }

    private function setMail(): void
    {

        $this->subject = $this->normalizeStringValue($this->data['Assunto'] ?? null);
        $this->message = $this->normalizeStringValue($this->data['Mensagem'] ?? null);
        $this->senderName = $this->normalizeNullableField($this->data['RemetenteNome'] ?? null);
        $this->senderEmail = $this->normalizeStringValue($this->data['RemetenteEmail'] ?? null);
        $this->recipientName = $this->normalizeNullableField($this->data['DestinoNome'] ?? null);
        $this->recipientEmail = $this->normalizeStringValue($this->data['DestinoEmail'] ?? null);
        $this->data = [];
    }

    // PRIVATE METHODS

    // Limpa código e espaços!

    private function config(): void
    {

        $this->mail->CharSet = 'utf-8';
        $this->mail->setLanguage('pt');
        $this->mail->isSMTP();
        $this->mail->isHTML(true);

        $this->mail->From = MAIL_USER;
        $this->mail->FromName = MAIL_SENDER;
        $this->mail->addReplyTo($this->senderEmail, $this->senderName ?? '');

        $this->mail->Subject = $this->subject;
        $this->mail->msgHTML($this->message);
        $this->mail->addAddress($this->recipientEmail, $this->recipientName ?? '');
    }

    // Recupera e separa os atributos pelo Array Data.

    private function sendMail(): void
    {

        if ($this->mail->send()) {
            $this->error = null;
            $this->result = true;
        } else {
            $this->error = '<b>ERRO AO enviar E-MAIL:</b> ' . $this->mail->ErrorInfo;
            $this->result = false;
        }

        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
    }

    /**
     * <b>enviar Anexo:</b> Efetue o Upload da imagem com a classe de upload. Com o getResult() deste envio, basta
     * anexar ao e-mail!
     */
    public function addFile(mixed $File): void
    {

        $path = $this->normalizeAttachmentPath($File);
        if (null === $path) {
            $this->file = false;

            return;
        }

        $this->file = $this->mail->addAttachment($path);
    }

    private function normalizeAttachmentPath(mixed $value): ?string
    {

        if (is_string($value) && '' !== $value) {
            return $value;
        }

        if (is_array($value) && isset($value['tmp_name']) && is_string($value['tmp_name'])) {
            return $value['tmp_name'];
        }

        return null;
    }

    /**
     * <b>Verificar Envio:</b> Executando um getResult é possível verificar se foi ou não efetuado
     * o envio do e-mail. Para mensagens execute o getError();.
     * @return bool $Result = TRUE or FALSE
     */
    public function getResult(): bool
    {

        return $this->result;
    }

    /**
     * <b>Obter Erro:</b> Retorna a última mensagem de erro ou null quando não houver falhas.
     */
    public function getError(): ?string
    {

        return $this->error;
    }
}
