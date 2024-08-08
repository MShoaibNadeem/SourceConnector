<?php
namespace App\Connectors;
use Exception;
use InfluxDB2\Client;
use Illuminate\Support\Facades\Response;

class InfluxDBConnector implements ConnectionTesterInterface
{
    public function testConnection($type,$name,$configurations)
    {
        if($name=='InfluxDB2')
        {
            $client = new Client([
                'url' => $configurations['url'],
                'token' => $configurations['token'],
                'bucket' => $configurations['bucket'],
                'org' => $configurations['org'],
            ]);
        }
        else{
            $client = new Client([
                $configurations['host'],
                $configurations['port'],
                $configurations['username'],
                $configurations['password'],
                $configurations['database'],
            ]
            );
        }


        try {
            $client->ping();
            return Response::success('Connection Successful',200);
        } catch (Exception $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }
}
