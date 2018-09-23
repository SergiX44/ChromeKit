<?php

namespace SergiX44\ChromeKit;


use SergiX44\ChromeKit\Exceptions\ChromeFailedException;
use Symfony\Component\Process\Process;

class ChromeKit
{

	const ACTION_SCREENSHOT = '--screenshot=';
	const ACTION_PDF = '--print-to-pdf=';
	const ACTION_HTML = '--dump-dom';
	const ACTION_REPL = '--repl';

	/** @var Process */
	protected $chrome;

	/** @var string */
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
	 * @param string $path
	 * @return $this
	 */
	public function chromePath(?string $path): ChromeKit
	{
		$this->chromeExecutablePath = $path;
		return $this;
	}

	/**
	 * @param string $url
	 * @param $chromePath
	 * @return ChromeKit
	 */
	public static function navigate(string $url, $chromePath = null): ChromeKit
	{
		return (new static)->url($url)->chromePath($chromePath);
	}

	/**
	 * @param string $url
	 * @return $this
	 */
	public function url(string $url): ChromeKit
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function enableGpu(): ChromeKit
	{
		$this->gpuEnabled = true;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function disableGpu(): ChromeKit
	{
		$this->gpuEnabled = false;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function showScrollbars(): ChromeKit
	{
		$this->showScrollbars = true;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function hideScrollbars(): ChromeKit
	{
		$this->showScrollbars = false;
		return $this;
	}

	/**
	 * @param int|null $width
	 * @param int|null $height
	 * @return $this
	 */
	public function screenshot($width = null, $height = null): ChromeKit
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
	public function pdf(): ChromeKit
	{
		$this->action = self::ACTION_SCREENSHOT;
		return $this;
	}

	/**
	 * @param string $userAgent
	 * @return $this
	 */
	public function userAgent(string $userAgent): ChromeKit
	{
		$this->userAgent = $userAgent;
		return $this;
	}

	/**
	 * @return string
	 * @throws Exceptions\UnsupportedEnvironment
	 */
	public function html(): string
	{
		$this->action = self::ACTION_HTML;

		$this->run($this->buildArgs());

		return $this->chrome->getOutput();
	}

	/**
	 * @param string $path
	 * @return bool
	 * @throws Exceptions\UnsupportedEnvironment
	 */
	public function save(string $path): bool
	{
		$this->outputPath = realpath(dirname($path)) . '/' . basename($path);

		return $this->run($this->buildArgs());
	}

	/**
	 * @return array
	 * @throws Exceptions\UnsupportedEnvironment
	 */
	protected function buildArgs(): array
	{
		$args = [$this->chromeExecutablePath !== null ? $this->chromeExecutablePath : FindChrome::forThisOS(), '--headless'];

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
	 * @return bool
	 */
	protected function run(array $args): bool
	{
		$this->chrome = new Process($args);

		if ($this->timeout > 0) {
			$this->chrome->setTimeout($this->timeout);
		}

		$this->chrome->run();

		if (!$this->chrome->isSuccessful()) {
			throw new ChromeFailedException($this->chrome);
		}

		return true;
	}
}