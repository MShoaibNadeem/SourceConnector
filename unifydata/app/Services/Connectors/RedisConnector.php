<?php

namespace App\Services\Connectors;

use Predis\Client;
use Exception;

class RedisConnector implements ConnectorInterface
{
    public function testConnection($type,$name,$configurations)
    {
        $client = new Client([
            'scheme' => $configurations['scheme'] ?? 'tcp',
            'host'   => $configurations['host'] ?? '127.0.0.1',
            'port'   => $configurations['port'] ?? 6379,
        ]);

        try {
            $client->connect();
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }
}
