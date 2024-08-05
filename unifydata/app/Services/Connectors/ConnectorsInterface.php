<?php

namespace App\Services\Connectors;

interface ConnectorInterface
{
    public function testConnection($type,$name,array $configurations);
}
