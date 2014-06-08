<?php
namespace Polyglot\Services;

use Polyglot\TestCases\PolyglotTestCase;

class UrlGeneratorTest extends PolyglotTestCase
{
	public function testCanGetLocaleFromUrl()
	{
		$locale = $this->url->locale();
		$this->assertEquals('fr', $locale);

		$request = $this->mockRequest();
		$request->shouldReceive('segment')->with(1)->andReturn('ds');
		$this->mockUrl($request);

		$this->assertEquals('fr', $this->url->locale());
	}

	public function testCanGetUrlToLanguage()
	{
		$request = $this->mockRequest();
		$request->shouldReceive('getScheme')->andReturn('http');
		$request->shouldReceive('root')->andReturn('http://localhost');
		$request->shouldReceive('getPathInfo')->andReturn('/admin/users');
		$this->mockUrl($request);

		$this->assertEquals('http://localhost/en', $this->url->language('en'));
	}

	public function testCanGetUrlToLanguageWhenInAnotherLanguage()
	{
		$request = $this->mockRequest();
		$request->shouldReceive('getScheme')->andReturn('http');
		$request->shouldReceive('root')->andReturn('http://localhost');
		$request->shouldReceive('getPathInfo')->andReturn('/fr');
		$this->mockUrl($request);

		$this->assertEquals('http://localhost/en', $this->url->switchLanguage('en'));
	}

	public function testCanGetUrlToSamePageInAnotherLanguage()
	{
		$request = $this->mockRequest();
		$request->shouldReceive('getScheme')->andReturn('http');
		$request->shouldReceive('root')->andReturn('http://localhost');
		$request->shouldReceive('getPathInfo')->andReturn('/admin/users');
		$this->mockUrl($request);

		$this->assertEquals('http://localhost/en/admin/users', $this->url->switchLanguage('en'));
	}

	public function testCanGetUrlToSamePageInAnotherLanguageWhenInALanguage()
	{
		$request = $this->mockRequest();
		$request->shouldReceive('getScheme')->andReturn('http');
		$request->shouldReceive('root')->andReturn('http://localhost');
		$request->shouldReceive('getPathInfo')->andReturn('/fr/admin/users');
		$this->mockUrl($request);

		$this->assertEquals('http://localhost/en/admin/users', $this->url->switchLanguage('en'));
	}
}
