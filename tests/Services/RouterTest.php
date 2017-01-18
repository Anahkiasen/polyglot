<?php

namespace Polyglot\Services;

use Polyglot\TestCases\PolyglotTestCase;

class RouterTest extends PolyglotTestCase
{
    public function testCanGetRoutesPrefix()
    {
        $prefix = $this->router->getRoutesPrefix(['before' => 'auth']);
        $this->assertEquals(['before' => 'auth'], $prefix);
    }

    public function testReturnsGroupIfLocaleIsDefault()
    {
        $this->app['request'] = $this->mockRequest('en');

        $prefix = $this->router->getRoutesPrefix(['before' => 'auth']);
        $this->assertEquals(['before' => 'auth', 'prefix' => 'en'], $prefix);
    }

    public function testCanMergeGroupPrefixes()
    {
        $this->app['request'] = $this->mockRequest('en');

        $prefix = $this->router->getRoutesPrefix(['before' => 'auth', 'prefix' => 'foo']);
        $this->assertEquals(['before' => 'auth', 'prefix' => 'en/foo'], $prefix);
    }

    public function testReturnGroupIfLocaleIsInvalid()
    {
        $this->app['request'] = $this->mockRequest('admin');

        $prefix = $this->router->getRoutesPrefix(['before' => 'auth']);
        $this->assertEquals(['before' => 'auth'], $prefix);
    }

    public function testCanCreateActualGroups()
    {
        $this->app['request'] = $this->mockRequest('en');
        $router = $this->router;

        $this->router->groupLocale(['before' => 'auth'], function () use ($router) {
            $router->get('foobar', 'Fake@foobar');
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
            $router->get('foobar', 'Fake@foobar');
        });

        foreach ($this->router->getRoutes() as $r) {
            $route = $r;
        }

        $this->assertEquals('en/foobar', $route->getPath());
    }
}
