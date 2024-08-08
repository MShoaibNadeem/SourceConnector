<?php
namespace App\Connectors;
use Exception;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Response;


class ElasticsearchConnector implements ConnectionTesterInterface
{
    public function testConnection($type,$name,$configurations)
    {
        $client = ClientBuilder::create()
            ->setHosts($configurations['hosts'])
            ->build();

        try {
            $client->ping();
            return Response::success('Connection Successful',200);
        } catch (Exception $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }
}
