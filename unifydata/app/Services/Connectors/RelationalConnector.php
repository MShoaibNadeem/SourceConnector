<?php

namespace App\Services\Connectors;

use PDO;
use PDOException;
use Gemini\Laravel\Facades\Gemini;

class RelationalConnectors implements ConnectorInterface
{
    public function testConnection($type,$name,$configurations)
    {

        $connectionString = $this->fetchConnectionStringFromGemini($type,$name,$configurations);
        try {
            $pdo = new PDO($connectionString, $configurations['username'], $configurations['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return ['success' => true, 'message' => 'Connection successful'];
        } catch (PDOException $e) {
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
