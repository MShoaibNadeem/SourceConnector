<?php

namespace App\Services;

use App\Factories\ConnectionTesterFactory;

class TestService
{
    public function testSourceConnection(array $sourceConfig): bool
    {
        $tester = ConnectionTesterFactory::create($sourceConfig['type'],$sourceConfig['name']);
        return $tester->testConnection($sourceConfig['config']);
    }
}
