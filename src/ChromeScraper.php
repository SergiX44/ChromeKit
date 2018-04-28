<?php
/**
 * Created by PhpStorm.
 * User: Sergio
 * Date: 24/04/2018
 * Time: 21:23
 */

namespace SergiX44\ChromeKit;


use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;

class ChromeScraper
{

	protected $input;
	protected $process;

	public function __construct(array $args)
	{
		$this->process = new Process($args);

		$this->input = new InputStream();
		$this->process->setInput($this->input);

		$this->process->start();
		sleep(1);
		$this->process->clearOutput();
	}

	public function exec(string $command)
	{
		$this->input->write($command);
		return $this;
	}

}