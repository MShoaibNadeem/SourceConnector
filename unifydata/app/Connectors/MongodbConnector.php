<?php
namespace App\Connectors;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\Response;
use MongoDB\Exception\Exception as MongoException;

class MongoDBConnector implements ConnectionTesterInterface
{
    public function testConnection($type, $name, $configurations)
    {
        $host = $configurations['host'];
        $port = $configurations['port'];
        $database = $configurations['database'];

        $uri = "mongodb://{$host}:{$port}/{$database}";
        try {
            $client = new MongoClient($uri);
            $databases = $client->listDatabases();
            $databaseExists = false;
            // Check if the specified database is in the list
            foreach ($databases as $db) {
                if ($db->getName() === $database) {
                    $databaseExists = true;
                    break;
                }
            }
            if (!$databaseExists) {
                return Response::error('Connection failed','Database does not exist',404);
                // return ['success' => false, 'message' => 'Connection failed','error'=>'Database does not exist'];
            }
            $check = $client->selectDatabase($database);
            $check->command(['ping' => 1]);
            return Response::success('Connection Successful',200);
            // return ['success' => true, 'message' => 'Connection successful'];
        } catch (MongoException $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }

}
