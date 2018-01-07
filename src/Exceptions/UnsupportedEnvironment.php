<?php

namespace SergiX44\ChromeKit\Exceptions;


class UnsupportedEnvironment extends \Exception
{

    public static function unsupportedOS(string $name)
    {
        return new static("This OS is not currently supported ({$name}).");
    }

    public static function chromeNotFound()
    {
        return new static('Chrome executable cannot be found in the current environment.');
    }

}