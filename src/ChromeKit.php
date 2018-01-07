<?php

namespace SergiX44\ChromeKit;


use SergiX44\ChromeKit\Exceptions\ChromeFailedException;
use Symfony\Component\Process\Process;

class ChromeKit
{

    const ACTION_SCREENSHOT = '--screenshot=';
    const ACTION_PDF = '--print-to-pdf=';
    const ACTION_HTML = '--dump-dom';

    /** @var Process */
    protected $chrome;

    protected $chromeExecutablePath;

    /** @var int */
    protected $timeout = 30;

    /** @var string */
    protected $url;

    /** @var bool */
    protected $gpuEnabled = false;

    /** @var bool */
    protected $showScrollbars = false;

    /** @var int */
    protected $width;
    /** @var int */
    protected $height;

    /** @var string */
    protected $userAgent;

    /** @var string */
    protected $action;

    /** @var string */
    protected $outputPath;


    /**
     * ChromeKit constructor.
     * @throws Exceptions\UnsupportedEnvironment
     */
    public function __construct()
    {
        $this->chromeExecutablePath = FindChrome::forThisOS();
    }

	/**
	 * @param string $path
	 * @return $this
	 */
	public function chromePath(string $path)
    {
    	$this->chromeExecutablePath = $path;
    	return $this;
    }

    /**
     * @param string $url
     * @return ChromeKit
     */
    public static function navigate(string $url)
    {
        return (new static)->url($url);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return $this
     */
    public function enableGpu()
    {
        $this->gpuEnabled = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableGpu()
    {
        $this->gpuEnabled = false;
        return $this;
    }

    /**
     * @return $this
     */
    public function showScrollbars()
    {
        $this->showScrollbars = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function hideScrollbars()
    {
        $this->showScrollbars = false;
        return $this;
    }

    /**
     * @param int|null $width
     * @param int|null $height
     * @return $this
     */
    public function screenshot(int $width = null, int $height = null)
    {
        $this->action = self::ACTION_SCREENSHOT;

        if ($width !== null) {
            $this->width = $width;
        }

        if ($height !== null) {
            $this->height = $height;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function pdf()
    {
        $this->action = self::ACTION_SCREENSHOT;
        return $this;
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function userAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function html()
    {
        $this->action = self::ACTION_HTML;

        $this->run($this->buildArgs());

        return $this->chrome->getOutput();
    }

    /**
     * @param string $path
     * @return bool
     */
    public function save(string $path)
    {

        $this->outputPath = $path;

        $this->run($this->buildArgs());

        return true;
    }

    /**
     * @return array
     */
    protected function buildArgs()
    {
        $args = [$this->chromeExecutablePath, '--headless'];

        if (!$this->gpuEnabled) {
            $args[] = '--disable-gpu';
        }

        if (!$this->showScrollbars) {
            $args[] = '--hide-scrollbars';
        }

        if ($this->userAgent !== null) {
            $args[] = '--user-agent=' . $this->userAgent;
        }

        if ($this->action !== self::ACTION_HTML) {
            $args[] = $this->action . $this->outputPath;
        } else {
            $args[] = $this->action;
        }

        if ($this->action === self::ACTION_SCREENSHOT && $this->width !== null && $this->height !== null) {
            $args[] = "--window-size={$this->width},{$this->height}";
        }

        $args[] = $this->url;

        return $args;

    }

    /**
     * @param array $args
     */
    protected function run(array $args)
    {
        $this->chrome = new Process($args);

        if ($this->timeout > 0) {
            $this->chrome->setTimeout($this->timeout);
        }

        $this->chrome->run();

        if (!$this->chrome->isSuccessful()) {
            throw new ChromeFailedException($this->chrome);
        }
    }
}