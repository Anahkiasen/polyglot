<?php
class UrlGeneratorTest extends PolyglotTests
{
  public function testCanGetLocaleFromUrl()
  {
    $locale = $this->url->locale();
    $this->assertEquals('fr', $locale);

    $this->app['request'] = $this->mockRequest();
    $this->app['request']->shouldReceive('segment')->with(1)->andReturn('ds');

    $this->url->setRequest($this->app['request']);
    $this->assertEquals('fr', $this->url->locale());
  }
}