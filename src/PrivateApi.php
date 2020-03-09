<?php

namespace SouthCN\PrivateApi;

class PrivateApi
{
    protected static $guard = null;

    public static function app($name)
    {
        return new Repository($name, static::$guard);
    }

    public static function registerGuard(\Closure $guard): void
    {
        static::$guard = $guard;
    }
}
