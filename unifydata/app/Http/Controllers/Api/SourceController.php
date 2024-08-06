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

        $configurations = [
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'username'=>$request->input('username'),
            'password'=>$request->input('password'),
            'database' => $request->input('database'),
        ];


        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'host' => 'required|string',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'port' => 'required|string',
            'database' => 'required|string',
        ]);
        // dd($request->input('type'));
        // $configurations = [
        //     'name' => $request->input('name'),
        //     'type' => $request->input('type'),
        //     'base_url' => $request->input('base_url'),
        //     'auth_type' => $request->input('auth_type'),
        //     'auth_credentials' => $request->input('auth_credentials'),
        // ];

        // // Validate the request data
        // $validated= $request->validate([
        //     'name' => 'required|string',
        //     'type'=>'required|string',
        //     'base_url' => 'required|url',
        //     'auth_type' => 'required|string',
        //     'auth_credentials' => 'nullable|array',
        // ]);

        $type = $validated['type'];

        $name = $validated['name'];

        try {
            // Instantiate the appropriate connection tester
            $tester = ConnectionTesterFactory::create($type, $name);
            $result = $tester->testConnection($configurations);

            // dd($result);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Connection successful!' : 'Connection failed.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
            ]);
        }

    }

    private function fetchConnectionStringFromGemini($name, $host, $port, $database)
    {
        $prompt = "Generate a connection string for the source $name of with the following configurations: host:$host, port:$port, database:$database.";

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

