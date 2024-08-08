<?php
namespace App\Connectors;
use Exception;
use Predis\Client;
use Illuminate\Support\Facades\Response;

class RedisConnector implements ConnectionTesterInterface
{
    public function testConnection($type,$name,$configurations)
    {
        $client = new Client([
            'scheme' => $configurations['scheme'] ?? 'tcp',
            'host'   => $configurations['host'],
            'port'   => $configurations['port'],
        ]);

        try {
            $client->connect();
            return Response::success('Connection Successful',200);
        } catch (Exception $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }
}
