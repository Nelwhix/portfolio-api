<?php

namespace Nelwhix\PortfolioApi;

class Validator
{
    public static function string(string $value): bool  {
        return strlen(trim($value)) === 0;
    }

    public static function email(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}