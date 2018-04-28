<?php

namespace SergiX44\ChromeKit;


use SergiX44\ChromeKit\Exceptions\UnsupportedEnvironment;

class FindChrome
{
    protected $paths = [
        'Darwin' => [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/Applications/Google Chrome Canary.app/Contents/MacOS/Google Chrome Canary',
            '/Applications/Chromium.app/Contents/MacOS/Chromium',
        ],
        'Linux' => [
            '/opt/google/chrome/chrome',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium',
        ],
	    'WINNT' => [
	    	'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
	    	'C:\Program Files\Google\Chrome\Application\chrome.exe',
	    ]
    ];

    /**
     * Gets Google Chrome path by OS
     * @param string $name
     * @return string
     * @throws UnsupportedEnvironment
     */
    public function getPathByOS(string $name)
    {
        if (!array_key_exists($name, $this->paths)) {
            throw UnsupportedEnvironment::unsupportedOS($name);
        }

        foreach ($this->paths[$name] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw UnsupportedEnvironment::chromeNotFound();
    }

    /**
     * Gets Chrome path by current OS
     * @return string
     * @throws UnsupportedEnvironment
     */
    public static function forThisOS()
    {
        return (new static)->getPathByOS(PHP_OS);
    }


}