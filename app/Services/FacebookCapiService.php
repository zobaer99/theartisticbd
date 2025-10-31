<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    protected $client;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client(['timeout' => 10]);
    }

    /**
     * Send events to Facebook CAPI
     *
     * @param string $pixelId
     * @param string $accessToken
     * @param array $payload
     * @return array response
     */
    public function sendEvent(string $pixelId, string $accessToken, array $payload)
    {
        $url = "https://graph.facebook.com/v14.0/{$pixelId}/events";

        try {
            $response = $this->client->post($url, [
                'query' => ['access_token' => $accessToken],
                'json' => $payload,
            ]);

            $body = json_decode((string) $response->getBody(), true);
            Log::info('Facebook CAPI response', ['response' => $body]);
            return $body;
        } catch (\Exception $e) {
            Log::error('Facebook CAPI error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
