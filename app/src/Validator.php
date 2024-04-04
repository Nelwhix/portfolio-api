<?php

namespace Nelwhix\PortfolioApi;

class Validator
{
    public static function string(?string $value = ""): bool  {
        if ($value === null) return false;

        return strlen(trim($value)) !== 0;
    }

    public static function email(string $value = ""): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function url(string $value = ""): bool {
        return filter_var($value, FILTER_VALIDATE_URL);
    }
}