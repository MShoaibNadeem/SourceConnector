<?php

namespace App\Factories;


use App\Connectors\ApiConnector;
use App\Connectors\ElasticsearchConnector;
use App\Connectors\InfluxDBConnector;
use App\Connectors\MongodbConnector;
use App\Connectors\RedisConnector;
use App\Connectors\RelationalConnector;
use App\Connectors\ConnectionTesterInterface;

class ConnectionTesterFactory
{
    //Initiating relivent tester on basis of source type
    public static function create($type, $name): ConnectionTesterInterface
    {
        switch ($type) {
            case 'Database':
                $relationalDatabases = [
                    'PostgreSQL',
                    'MySQL',
                    'SQLite',
                    'Oracle Database',
                    'Microsoft SQL Server',
                    'IBM Db2',
                    'MariaDB',
                    'SAP HANA',
                    'Amazon Aurora',
                    'Google Cloud SQL',
                    'Teradata',
                    'Snowflake',
                    'Informix',
                    'Sybase'
                ];
                if (in_array($name, $relationalDatabases)) {
                    return new RelationalConnector();
                }
                switch ($name) {
                    case 'MongoDB':
                        return new MongodbConnector();
                    case 'InfluxDB':
                        return new InfluxDBConnector();
                    case 'InfluxDB2':
                        return new InfluxDBConnector();
                    case 'Elasticsearch':
                        return new ElasticsearchConnector();
                    case 'Redis':
                        return new RedisConnector();
                }
            case 'API':
                return new ApiConnector();
            default:
                throw new \InvalidArgumentException("Unsupported connection type: $type");
        }
    }
}
