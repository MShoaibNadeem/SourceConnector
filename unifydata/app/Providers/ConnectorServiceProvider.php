<?php

namespace App\Providers;

use App\Services\Connectors\InfluxDBConnector;
use App\Services\Connectors\RedisConnector;
use App\Services\Connectors\RelationalConnectors;
use Illuminate\Support\ServiceProvider;
use App\Services\Connectors\{
    MySQLConnector,
    PostgreSQLConnector,
    MongoDBConnector,
    ElasticsearchConnector,
    ApiConnector
};
use App\Services\Connectors\ConnectorInterface;

class ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ConnectorInterface::class, function ($app, $parameters) {
            $type = $parameters['type'] ?? '';
            $name = $parameters['name'] ?? '';

            switch ($type) {
                case 'Database':
                    switch ($name) {
                        case 'MySQL' || 'PostgreSQL' || 'SQLite' || 'Oracle Database' || 'Microsoft SQL Server' || 'IBM Db2':
                            return new RelationalConnectors();
                        case 'MongoDB':
                            return new MongoDBConnector();
                        case 'Elasticsearch':
                            return new ElasticsearchConnector();
                        case 'InfluxDB':
                            return new InfluxDBConnector();
                        case 'Redis':
                            return new RedisConnector();
                    }
                case 'API':
                    return new ApiConnector();
                default:
                    throw new \InvalidArgumentException("Unsupported connector type: $type");
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
