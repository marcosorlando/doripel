<?php

namespace App\Helpers;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function is_string;
use function json_decode;
use function sprintf;

/**
 * Instagram [ HELPER ]
 * Simples classe para obter imagens do instagram.
 * @copyright (c) year, Robson V. Leite - UPINSIDE TECNOLOGIA
 */
class Instagram
{
    /**
     * @param string $userId identificador do usuário obtido via OAuth
     * @param string $accessToken token de acesso gerado pelo OAuth
     */
    public function __construct(
        private readonly string $userId,
        private readonly string $accessToken
    ) {
    }

    /**
     * Obtém os últimos posts do usuário (até 20).
     */
    public function getRecent(): mixed
    {

        $endpoint = sprintf(
            'https://api.instagram.com/v1/users/%s/media/recent/?access_token=%s',
            $this->userId,
            $this->accessToken
        );

        return $this->request($endpoint);
    }

    private function request(string $endpoint): mixed
    {

        $ch = curl_init($endpoint);
        if (false === $ch) {
            return null;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);

        $response = curl_exec($ch);
        curl_close($ch);

        if (false === $response) {
            return null;
        }

        if (!is_string($response)) {
            return null;
        }

        return json_decode($response, true);
    }

    public function getTags(string $tag): mixed
    {

        $endpoint = sprintf(
            'https://api.instagram.com/v1/tags/%s/media/recent?access_token=%s',
            $tag,
            $this->accessToken
        );

        return $this->request($endpoint);
    }
}
