<?php

namespace Nelwhix\PortfolioApi;

class Helpers
{
    public static function isAuthorized(String $token): bool {
        $secret_Key  = '68V0zWFrS72GbpPreidkQFLfj4v9m3Ti+DXc8OB0gcM=';
        $token = JWT::decode($jwt, $secret_Key, ['HS512']);
        $now = new DateTimeImmutable();
        $serverName = "your.domain.name";

        if ($token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            return false;
        }

        return true;
    }
}