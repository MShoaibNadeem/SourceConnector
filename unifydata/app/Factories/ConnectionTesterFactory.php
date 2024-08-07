<?php

namespace App\Factories;


use App\Connectors\ApiConnector;
use App\Connectors\MongodbConnector;
use App\Connectors\RelationalConnector;
use App\Connectors\ConnectionTesterInterface;

class ConnectionTesterFactory
{
    public static function create($type,$name): ConnectionTesterInterface
    {
        switch ($type) {
            case 'Database':
                switch ($name){
                    case "PostgresSQL":
                        return new RelationalConnector();
                    case "MongoDB":
                        return new MongodbConnector();
                    }
            case 'API':
                return new ApiConnector();
            default:
                throw new \InvalidArgumentException("Unsupported connection type: $type");
        }
    }
}
