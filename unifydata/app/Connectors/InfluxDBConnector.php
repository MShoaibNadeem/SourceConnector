<?php
namespace App\Connectors;
use InfluxDB2\Client;
use Exception;

class InfluxDBConnector implements ConnectionTesterInterface
{
    public function testConnection($type,$name,$configurations)
    {
        $client = new Client([
            'url' => $configurations['url'],
            'token' => $configurations['token'],
            'bucket' => $configurations['bucket'],
            'org' => $configurations['org'],
        ]);

        try {
            $client->ping();
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }
}
