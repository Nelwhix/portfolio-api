<?php

namespace Nelwhix\PortfolioApi;

use Carbon\CarbonImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Helpers
{
    public static function isAuthorized(String $token): bool {
        $secret_Key  = $_ENV['JWT_SECRET'];

        try {
            $token = JWT::decode($token, new Key($secret_Key, 'HS512'));
        } catch (\Exception) {
            return false;
        }

        $now = new CarbonImmutable();
        $serverName = $_ENV['API_URL'];

        if ($token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            return false;
        }

        return true;
    }
}