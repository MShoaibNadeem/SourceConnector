<?php
namespace App\Connectors;

use Gemini\Laravel\Facades\Gemini;
use MongoDB\Client as MongoClient;
use MongoDB\Exception\Exception as MongoException;

class MongoDBConnector implements ConnectionTesterInterface
{
    public function testConnection($configurations)
    {
        $host = $configurations['host'];
        $port = $configurations['port'];
        $database = $configurations['database'];
        $username = $configurations['username'] ?? '';
        $password = $configurations['password'] ?? '';
        $authSource = $configurations['authSource'] ?? '';
        $options = $configurations['options'] ?? [];

        $uri = "mongodb://";
        if ($username && $password) {
            $uri .= "{$username}:{$password}@";
        }
        $uri .= "{$host}:{$port}/{$database}";

        if ($authSource) {
            $uri .= "?authSource=$authSource";
        }

        if ($options) {
            // Add additional options to the connection string
            foreach ($options as $key => $value) {
                $uri .= "&$key=$value";
            }
        }

        // dd($uri);

        try {
            $client = new MongoClient($uri);
            $database = $client->selectDatabase($configurations['database']);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (MongoException $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }

}
