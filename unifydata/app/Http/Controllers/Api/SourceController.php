<?php

namespace App\Http\Controllers\Api;

use PDOException;
use App\Models\Source;
use App\Models\AvailableSource;
use App\Models\SourceRequirements;
use Gemini\Laravel\Facades\Gemini;
use App\Http\Controllers\Controller;
use App\Factories\ConnectionTesterFactory;
use Illuminate\Http\Request;

// use Illuminate\Container\Container;
// use App\Services\Connectors\ConnectorInterface;
// use Illuminate\Foundation\Exceptions\Renderer\Exception;


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

    public function getAvailableSources()
    {
        $availableSources = AvailableSource::all();
        return response()->json($availableSources);
    }

    public function getConnectorRequirements($id)
    {
        $source = AvailableSource::select('type', 'name')->where('_id', '=', $id)->firstOrFail();
        $type = $source->type;
        $name = $source->name;

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
    public function testConnection(Request $request)
    {

        // $source = AvailableSource::select('type', 'name')->where('_id', '=', $id)->firstOrFail();
        // $type = $source->type;
        // $name = $source->name;
        $type = 'Database';
        $name = "PostgresSQL";

        $request->merge([
            'type' => $type,
            'name' => $name,
        ]);
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'base_url' => 'required_if:type,API|url',
            'auth_type' => 'required_if:type,API|string',
            'auth_credentials' => 'required_if:type,API|array|nullable',
            'host' => 'required_if:type,Database|string',
            'port' => 'required_if:type,Database|string',
            'username' => 'required_if:type,Database|string|nullable',
            'password' => 'required_if:type,Database|string|nullable',
            'database' => 'required_if:type,Database|string',
            // Add other conditional rules as needed for different types
        ]);
        // Extract validated data and filter out null values
        $configurations = array_filter([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'base_url' => $validated['base_url'] ?? null,
            'auth_type' => $validated['auth_type'] ?? null,
            'auth_credentials' => $validated['auth_credentials'] ?? null,
            'host' => $validated['host'] ?? null,
            'port' => $validated['port'] ?? null,
            'username' => $validated['username'] ?? null,
            'password' => $validated['password'] ?? null,
            'database' => $validated['database'] ?? null,
        ], function ($value) {
            return !is_null($value);
        });

        $type = $validated['type'];
        $name = $validated['name'];

        try {
            // Instantiate the appropriate connection tester
            $tester = ConnectionTesterFactory::create($type, $name);
            $result = $tester->testConnection($configurations);

            // dd($result);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Connection successful!' : 'Connection failed: ' . $result['error'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ]);
        }

    }

    // public function createSource(Request $request, $id)
    // {
    //     Source::create([
    //         'name' => $configurations['name'],
    //         'type' => $configurations['type'],
    //         'config' => json_encode($configurations),
    //     ]);
    // }


}

