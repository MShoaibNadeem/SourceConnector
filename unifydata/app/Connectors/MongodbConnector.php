<?php
namespace App\Connectors;

use MongoDB\Client as MongoClient;
use MongoDB\Exception\Exception as MongoException;

class MongoDBConnector implements ConnectionTesterInterface
{
    public function testConnection($type, $name, $configurations)
    {
        $host = $configurations['host'];
        $port = $configurations['port'];
        $database = $configurations['database'];
        // $username = $configurations['username'] ?? '';
        // $password = $configurations['password'] ?? '';
        // $authSource = $configurations['authSource'] ?? '';
        // $options = $configurations['options'] ?? [];

        $uri = "mongodb://{$host}:{$port}/{$database}";
        // if ($username && $password) {
        //     $uri .= "{$username}:{$password}@";
        // }

        // if ($authSource) {
        //     $uri .= "?authSource=$authSource";
        // }

        // if ($options) {
        //     // Add additional options to the connection string
        //     foreach ($options as $key => $value) {
        //         $uri .= "&$key=$value";
        //     }
        // }


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
                return ['success' => false, 'message' => 'Connection failed','error'=>'Database does not exist'];
            }
            $check = $client->selectDatabase($database);
            $check->command(['ping' => 1]);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (MongoException $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }

}
