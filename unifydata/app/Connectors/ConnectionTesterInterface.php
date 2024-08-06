<?php

namespace App\Connectors;

interface ConnectionTesterInterface
{
    public function testConnection(array $configurations);
}
