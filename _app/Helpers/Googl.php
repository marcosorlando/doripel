<?php

namespace App\Helpers;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;

class Googl
{
    private readonly string $apiUrl;

    public function __construct(private readonly string $apiKey)
    {
        $this->apiUrl = 'https://www.googleapis.com/urlshortener/v1/url?fields=id%2ClongUrl&key=' . $this->apiKey;
    }

    public function short(string $link): ?string
    {

        $payload = json_encode([
            'longUrl' => $link,
            'key' => $this->apiKey,
        ]);

        if (false === $payload) {
            return null;
        }

        $ch = curl_init();
        if (false === $ch) {
            return null;
        }
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type:application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        curl_close($ch);

        if (false === $response) {
            return null;
        }

        if (!is_string($response)) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            return null;
        }

        $id = $decoded['id'] ?? null;

        return is_string($id) ? $id : null;
    }
}
