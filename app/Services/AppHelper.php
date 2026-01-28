<?php
namespace App\Services;

class AppHelper
{
    public static function isLocal()
    {
        return config('app.env') === 'local';
    }
}