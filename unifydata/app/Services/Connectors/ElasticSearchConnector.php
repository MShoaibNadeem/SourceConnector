<?php

namespace App\Services\Connectors;

use Exception;
use Elastic\Elasticsearch\ClientBuilder;


class ElasticsearchConnector implements ConnectorInterface
{
    public function testConnection($type,$name,$configurations)
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
