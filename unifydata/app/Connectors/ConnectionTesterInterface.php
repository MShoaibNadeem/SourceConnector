<?php

namespace App\Connectors;

interface ConnectionTesterInterface
{
    public function testConnection($type,$name,array $configurations);
}
