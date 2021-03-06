<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 07/01/18
 * Time: 0.07
 */

namespace SergiX44\ChromeKit\Test;


use PHPUnit\Framework\TestCase;
use SergiX44\ChromeKit\ChromeKit;

class ChromeKitTests extends TestCase
{

	public static function tearDownAfterClass()
	{
		parent::tearDownAfterClass();

		$files = ['./screen.png', './screen1.png', './file.pdf'];

		foreach ($files as $f) {
			if (file_exists($f)) {
				unlink($f);
			}
		}
    }

	/** @test
	 * @throws \SergiX44\ChromeKit\Exceptions\UnsupportedEnvironment
	 */
	public function it_returns_html()
	{
		$html = ChromeKit::navigate('./index.html')->html();

		$this->assertNotEmpty($html);
	}

	/** @test
	 * @throws \SergiX44\ChromeKit\Exceptions\UnsupportedEnvironment
	 */
	public function it_can_take_screenshots()
	{
		ChromeKit::navigate('./index.html')->screenshot()->save('./screen.png');
		$this->assertFileExists('./screen.png');
	}

	/** @test
	 * @throws \SergiX44\ChromeKit\Exceptions\UnsupportedEnvironment
	 */
	public function it_can_take_screenshots_with_size()
	{
		ChromeKit::navigate('./index.html')->screenshot(20, 30)->save('./screen1.png');
		$this->assertFileExists('./screen1.png');
	}

	/** @test
	 * @throws \SergiX44\ChromeKit\Exceptions\UnsupportedEnvironment
	 */
	public function it_can_save_pdf()
	{
		ChromeKit::navigate('./index.html')->pdf()->save('./file.pdf');
		$this->assertFileExists('./file.pdf');
	}
}