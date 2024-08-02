<?php

namespace App\Http\Controllers\Api;

use App\Models\Source;
use App\Models\AvailableSource;
use App\Models\SourceRequirements;
use Gemini\Laravel\Facades\Gemini;
use OpenAI\Client as OpenAIClient;
use App\Http\Controllers\Controller;


class SourceController extends Controller
{
    // GET /sources/existing
    public function index()
    {
        $sources = Source::all();
        return response()->json($sources);
    }
    public function search($name)
    {
        $source = AvailableSource::where('name', $name)->get();

        return response()->json($source);
    }

    public function getConnectorRequirements($id)
    {
        $source = AvailableSource::select('type', 'name')->where('_id', '=', $id)->firstOrFail();
        $type = $source->type;
        $name = $source->name;

        // $type->makeHidden('_id');
        // $name->makeHidden('_id');

        $requirements = SourceRequirements::where('type', $type)->where('name', $name)->first();

        if ($requirements) {
            // Return the stored requirements
            return response()->json(json_decode($requirements->requirements, true));
        }

        // Query OpenAI for the required fields for this type
        $requirements = $this->fetchRequirementsFromOpenAI($type, $name);

        // Ensure $type and $name are JSON encoded if they are arrays/objects
        $typeJson = is_array($type) || is_object($type) ? json_encode($type) : $type;
        $nameJson = is_array($name) || is_object($name) ? json_encode($name) : $name;

        SourceRequirements::create([
            'type' => $typeJson,
            'name' => $nameJson,
            'requirements' => json_encode($requirements)
        ]);

        return response()->json($requirements);

    }
    private function fetchRequirementsFromOpenAI($type, $name)
    {
        $prompt = "I want to recieve custom configurations from a user for the selected source $name its type is $type provide me with all the necessary parameters required to establish a connection with a live source along with the optional security parameters if neeeded make a document attributes with necessary and optional parameters as an objet init and each parameter in the objects should be a key and there description and data type should also be in the object of the key and provide me in json";

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
}
