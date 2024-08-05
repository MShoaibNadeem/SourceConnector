<?php

namespace App\Services\Connectors;

use Gemini\Laravel\Facades\Gemini;
use MongoDB\Client as MongoClient;
use MongoDB\Exception\Exception as MongoException;

class MongoDBConnector implements ConnectorInterface
{
    public function testConnection($type,$name,$configurations)
    {
        $connectionString = $this->fetchConnectionStringFromGemini($type,$name,$configurations);
        try {
            $client = new MongoClient($connectionString);
            $database = $client->selectDatabase($configurations['database']);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (MongoException $e) {
            return ['success' => false, 'message' => 'Connection failed', 'error' => $e->getMessage()];
        }
    }
    private function fetchConnectionStringFromGemini($type,$name,$configurations)
    {
        $prompt = "Generate a connection string for the source $name of with the following configurations:".json_encode($configurations);

        $response = Gemini::geminiPro()->generateContent($prompt);

        // Fetching only the required part from the response
        $textContent = $response->candidates[0]->content->parts[0]->text;

        // Removing ``` from beginning and end
        $trimmedText = trim($textContent, '`');
        echo $trimmedText;
        echo "\n";
        return $trimmedText;
    }
}
