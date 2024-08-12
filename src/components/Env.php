<?php

namespace hesabro\helpers\components;

class Env
{
    public static function get($name, $default = null, $required = false)
    {
        if (array_key_exists($name, $_ENV)) {
            $value = $_ENV[$name];
        } elseif (array_key_exists($name, $_SERVER)) {
            $value = $_SERVER[$name];
        } else {
            $value = getenv($name);
        }

        if ($value === false && $required) {
            throw new \Exception("Environment variable '$name' is not set");
        }

        if ($value === false) {
            return $default;
        }

        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}
