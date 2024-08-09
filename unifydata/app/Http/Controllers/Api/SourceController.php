<?php

namespace App\Http\Controllers\Api;

use App\Models\Source;
use Illuminate\Http\Request;
use App\Models\AvailableSource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Factories\ConnectionTesterFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class SourceController extends Controller
{
    // Get Exisitng Sources
    public function index()
    {
        // Fetch exisiting sources from
        $sources = Source::all();
        return response()->json($sources);
    }
    // Search Available Sources
    // 1 - Source name as parameter
    public function search($name)
    {
        $source = AvailableSource::where('name', 'LIKE', $name . '%')->get();
        if (count($source)==0) {
            throw new ModelNotFoundException('Source not found');
        }
        return response()->json($source);
    }
    // Get Available Sources
    public function getAvailableSources()
    {
        $availableSources = AvailableSource::all();
        return response()->json($availableSources);
    }
    public function testConnection(Request $request, $id)
    {
        // Extract validated data and filter out null values
        $configurations = $this->getConfig($request);
        //From request get type and name
        $type = $request['type'];
        $name = $request['name'];
        try {
            // Initiate the appropriate connection tester
            $tester = ConnectionTesterFactory::create($type, $name);
            $result = $tester->testConnection($type, $name, $configurations);
            return $result;
        } catch (\Exception $e) {
            return Response::error('Connection failed',$e->getMessage(),400);
        }
    }
    //Create Source with user configurations against source
    public function createSource(Request $request, $id)
    {
        //Fetch configuration from request and make an array of it
        $configurations = $this->getConfig($request);
        $availableSource=AvailableSource::getImage($id);
        $image=$availableSource->image;
        if($image)
        {
            $source=Source::createSource($request,$image,$configurations);
            return $source;
        }
        return $availableSource;
    }
    //Helper function to create config array
    private function getConfig($request)
    {
        //Filter request to get only relevant type Configuration i.e Api, Database
        $configurations = array_filter([
            'base_url' => $request['base_url'] ?? null,
            'auth_type' => $request['auth_type'] ?? null,
            'auth_credentials' => $request['auth_credentials'] ?? null,
            'host' => $request['host'] ?? null,
            'port' => $request['port'] ?? null,
            'username' => $request['username'] ?? null,
            'password' => $request['password'] ?? null,
            'database' => $request['database'] ?? null,
        ], function ($value) {
            return !is_null($value);
        });
        return $configurations;
    }
}

