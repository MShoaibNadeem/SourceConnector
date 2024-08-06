<?php
namespace App\Connectors;
use Exception;
use Elastic\Elasticsearch\ClientBuilder;


class ElasticsearchConnector implements ConnectionTesterInterface
{
    public function testConnection($configurations)
    {
        $client = ClientBuilder::create()
            ->setHosts($configurations['hosts'])
            ->build();

        try {
            $client->ping();
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }
}
