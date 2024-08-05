<?php

namespace App\Services\Connectors;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;

class ApiConnector implements ConnectorInterface
{
    public function testConnection($type, $name, array $configurations)
    {
        $url = $configurations['base_url'];
        $response = $this->makeAuthenticatedRequest($url, $configurations['auth_type'], $configurations['auth_credentials']);

        return $response->successful() ? [
            'success' => true,
            'message' => 'Connection successful',
            'data' => $response->json()
        ] : [
            'success' => false,
            'message' => 'Connection failed',
            'status' => $response->status()
        ];
    }
    private function makeAuthenticatedRequest($url, $authType, $authCredentials)
    {
        $client = Http::withOptions(['base_uri' => $url]);

        switch ($authType) {
            case 'No_Auth':
                $response = $client->get($url);
                break;
            case 'API_Key':
                $response = $this->handleApiKeyAuth($client, $url, $authCredentials);
                break;
            case 'Bearer':
                $response = $client->withToken($authCredentials['token'])->get($url);
                break;
            case 'Basic_HTTP':
                $response = $client->withBasicAuth($authCredentials['username'], $authCredentials['password'])->get($url);
                break;
            case 'Session_Token':
                $response = $client->withHeaders(['Session-Token' => $authCredentials['session_token']])->get($url);
                break;
            case 'OAuth':
                $response = $this->handleOAuthAuth($client, $authCredentials,$url);
                break;
            default:
                throw new \Exception('Invalid authentication type');
        }

        return $response;
    }

    private function handleApiKeyAuth($client, $url, $authCredentials)
    {
        $injectInto = $authCredentials['inject_into'];
        $paramName = $authCredentials['parameter_name'];
        $apiKey = $authCredentials['api_key'];

        switch ($injectInto) {
            case 'Query Parameter':
                $url = $this->injectApiKeyIntoUrl($url, $paramName, $apiKey);
                return $client->get($url);
            case 'Header':
                return $client->withHeaders([$paramName => $apiKey])->get($url);
            case 'Body data (urlencoded form)':
                return $client->asForm()->post($url, [$paramName => $apiKey]);
            case 'Body JSON payload':
                return $client->asJson()->post($url, [$paramName => $apiKey]);
            default:
                throw new \Exception('Invalid injection method');
        }
    }

    private function injectApiKeyIntoUrl($url, $paramName, $apiKey)
    {
        $parsedUrl = parse_url($url);
        $query = isset($parsedUrl['query']) ? $parsedUrl['query'] . '&' : '';
        $query .= urlencode($paramName) . '=' . urlencode($apiKey);

        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . $query;
    }
    private function handleOAuthAuth($client, $authCredentials,$url)
    {
        $tokenUrl = $authCredentials['token_url'];
        $clientId = $authCredentials['client_id'];
        $clientSecret = $authCredentials['client_secret'];
        $scopes = $authCredentials['scopes'];

        // Fetch OAuth token
        $tokenResponse = $client->asForm()->post($tokenUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => implode(' ', $scopes)
        ]);

        if ($tokenResponse->failed()) {
            throw new \Exception('Failed to obtain OAuth token: ' . $tokenResponse->body());
        }

        $accessToken = $tokenResponse->json()['access_token'];


        return $client->withToken($accessToken)->get($url);
    }
}
