<?php

namespace App\Http\Controllers\Api;

use App\Models\AvailableSource;
use App\Models\SourceRequirements;
use App\Models\SourceTemplate;
use Gemini\Laravel\Facades\Gemini;
use App\Http\Controllers\Controller;

class SourceTemplateController extends Controller
{
    //Get configuration requirements
    public function getConnectorRequirements($id)
    {
        $source = AvailableSource::getSourceById($id);
        $type = $source->type;
        $name = $source->name;
        // Fetch templates if exists in Collection else get from Gemini
        $requirements = SourceRequirements::checkTemplate($type, $name);
        if ($requirements) {
            return $requirements;
        } else {
            // Query OpenAI for the required fields for this type
            $requirements = $this->fetchRequirementsFromOpenAI($type, $name);
            // Store template in database
            SourceRequirements::createTemplate($type, $name, $requirements);
            return response()->json($requirements);
        }
    }
    //Fetch requirements from gemini
    private function fetchRequirementsFromOpenAI($type, $name)
    {
        $prompt = "I want to recieve custom configurations from a user for the selected etl source $name its type is $type provide me with all the necessary parameters required to establish a connection with a live source along with the optional security parameters if neeeded make a document attributes with necessary and optional parameters as an objet in it and each parameter in the objects should be a key and there description and data type should also be in the object of the key and provide me in json";
        // This is a placeholder function. You would use the OpenAI API here.
        $response = Gemini::geminiPro()->generateContent($prompt);
        //Fetching only required part from the response
        $textContent = $response->candidates[0]->content->parts[0]->text;
        //Removing json keyword from the response
        $jsonString = str_replace('json', '', $textContent);
        // Removing ``` from beginning and end
        $trimmedText = trim($jsonString, '`');
        // Decode the JSON string into a PHP associative array
        $jsonObject = json_decode($trimmedText, true);
        return response()->json($jsonObject);
    }

    public function getTemplateFromDatabase($id){
        $source=SourceTemplate::getRequirements($id);
        return response()->json($source[0]->fields);
    }
}
