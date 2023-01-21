<?php

namespace Nelwhix\PortfolioApi;

use MongoDB\Client;

class Database
{
    public \MongoDB\Database $database;

    public function __construct() {
        $connection = new Client('mongodb://database:27017', [
            'username' => 'nelwhix',
            'password' => 'admin',
            'ssl' => false,
        ]);

        $this->database = $connection->portfolio_api;
    }
}