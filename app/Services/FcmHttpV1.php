<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use RuntimeException;

class FcmHttpV1
{
    private Client $http;
    private string $projectId;
    private array $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
    private array $credentials;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => 'https://fcm.googleapis.com/',
            'timeout' => 10,
        ]);

        $this->projectId = config('services.firebase.project_id') ?? env('FIREBASE_PROJECT_ID', '');
        if (empty($this->projectId)) {
            throw new RuntimeException('Firebase project id is not configured.');
        }

        $credentialsPath = config('services.firebase.credentials_file')
            ?? base_path(env('FIREBASE_CREDENTIALS', ''));

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            throw new RuntimeException('Firebase credentials file not found.');
        }

        $credentialsContent = file_get_contents($credentialsPath);
        if ($credentialsContent === false) {
            throw new RuntimeException('Unable to read Firebase credentials file.');
        }

        $credentials = json_decode($credentialsContent, true);
        if (!is_array($credentials)) {
            throw new RuntimeException('Invalid Firebase credentials JSON.');
        }

        $this->credentials = $credentials;
    }

    private function accessToken(): string
    {
        $serviceAccount = new ServiceAccountCredentials($this->scopes, $this->credentials);
        $token = $serviceAccount->fetchAuthToken();

        if (!isset($token['access_token'])) {
            throw new RuntimeException('Unable to fetch Firebase access token.');
        }

        return $token['access_token'];
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $imageUrl = $data['image_url'] ?? $data['image'] ?? null;
        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'notification' => [
                        'channel_id' => 'high_importance',
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'mutable-content' => 1,
                        ],
                    ],
                ],
            ],
        ];

        if (!empty($imageUrl)) {
            $payload['message']['notification']['image'] = $imageUrl;
            $payload['message']['android']['notification']['image'] = $imageUrl;
            $payload['message']['apns']['fcm_options'] = [
                'image' => $imageUrl,
            ];
        }

        $this->http->post("v1/projects/{$this->projectId}/messages:send", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken(),
            ],
            'json' => $payload,
        ]);
    }
}
