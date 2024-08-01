<?php

namespace App\Http\Controllers\Api;

use App\Models\Source;
use App\Models\AvailableSource;
use App\Http\Controllers\Controller;
use OpenAI\Client as OpenAIClient;
use Gemini\Laravel\Facades\Gemini;


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
        $type = AvailableSource::select('type')->where('_id', '=', $id)->get();
        $name = AvailableSource::select('name')->where('_id', '=', $id)->get();

        $type->makeHidden('_id');
        $name->makeHidden('_id');

        // Query OpenAI for the required fields for this type
        $requirements = $this->fetchRequirementsFromOpenAI($type, $name);

        return response()->json($requirements);

    }
    private function fetchRequirementsFromOpenAI($type, $name)
    {
        $prompt = "I need to connect to a source in an ETL application. The source details are as follows:
        Name: $name
        Type: $type
        Please provide the necessary configuration parameters to connect to a source as a JSON object with key-value pairs. Provide only keys not values.
        Provide only those fields that are must required to establish a connection and skip optional ones.
        Should be same to the one's airbyte asks while creating a source";
        // This is a placeholder function. You would use the OpenAI API here.
        $response = Gemini::geminiPro()->generateContent($prompt);

        $textContent = $response->candidates[0]->content->parts[0]->text;

       // dd($textContent);


        // Trim any extra whitespace or newlines
        $trimmedText = trim($textContent);

        // dd($trimmedText);

        // // Decode the JSON string into a PHP associative array
        // $jsonObject = json_decode($trimmedText,true);
        // dd($jsonObject);
        // //$requirements = $response->text();
        // return response()->json($jsonObject);
        return $trimmedText;
    }
}
