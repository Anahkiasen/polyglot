<?php
namespace Polyglot;

use Polyglot\TestCases\PolyglotTestCase;

class RouterTest extends PolyglotTestCase
{
	public function testCanGetRoutesPrefix()
	{
		$prefix = $this->router->getRoutesPrefix(array('before' => 'auth'));
		$this->assertEquals(array('before' => 'auth'), $prefix);
	 }

	 public function testReturnsGroupIfLocaleIsDefault()
	 {
		$this->app['request'] = $this->mockRequest('en');

		$prefix = $this->router->getRoutesPrefix(array('before' => 'auth'));
		$this->assertEquals(array('before' => 'auth', 'prefix' => 'en'), $prefix);
	}

	public function testCanMergeGroupPrefixes()
	{
		$this->app['request'] = $this->mockRequest('en');

		$prefix = $this->router->getRoutesPrefix(array('before' => 'auth', 'prefix' => 'foo'));
		$this->assertEquals(array('before' => 'auth', 'prefix' => array('en', 'foo')), $prefix);
	}

	public function testReturnGroupIfLocaleIsInvalid()
	{
		$this->app['request'] = $this->mockRequest('admin');

		$prefix = $this->router->getRoutesPrefix(array('before' => 'auth'));
		$this->assertEquals(array('before' => 'auth'), $prefix);
	}

	public function testCanCreateActualGroups()
	{
		$this->app['request'] = $this->mockRequest('en');
		$router = $this->router;

		$this->router->groupLocale(array('before' => 'auth'), function () use ($router) {
			$router->get('foobar', 'foobar');
		});

		foreach ($this->router->getRoutes() as $r) {
			$route = $r;
		}

		$this->assertEquals('en/foobar', $route->getPath());
	}

	public function testCanCreateGroupsWithoutArrays()
	{
		$this->app['request'] = $this->mockRequest('en');
		$router = $this->router;

		$this->router->groupLocale(function () use ($router) {
			$router->get('foobar', 'foobar');
		});

		foreach ($this->router->getRoutes() as $r) {
			$route = $r;
		}

		$this->assertEquals('en/foobar', $route->getPath());
	}
}
