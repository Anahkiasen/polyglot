<?php
class LanguageTest extends PolyglotTests
{
  public function testCanGetCurrentLanguage()
  {
    $current = $this->app['translator']->getLocale();

    $this->assertEquals($current, $this->polyglotLang->current());
  }

  public function testCanChangeLanguage()
  {
    $this->polyglotLang->set('en');
    $current = $this->app['translator']->getLocale();

    $this->assertEquals('en', $current);
  }

  public function testCanCheckCurrentLanguage()
  {
    $this->polyglotLang->set('en');

    $this->assertTrue($this->polyglotLang->active('en'));
  }

  public function testCantSetUnexistingLocales()
  {
    $this->polyglotLang->set('ds');

    $this->assertEquals('fr', $this->polyglotLang->current());
  }

  public function testCanSetLocaleFromLanguage()
  {
    $locale = $this->polyglotLang->locale('en');
    $translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

    $this->assertContains($locale, array('en_US.UTF8', 'en_US'));
    $this->assertEquals('January', $translatedString);
  }

  public function testCanSetLocaleFromCurrent()
  {
    $this->polyglotLang->set('en');
    $locale = $this->polyglotLang->locale();
    $translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

    $this->assertContains($locale, array('en_US.UTF8', 'en_US'));
    $this->assertEquals('January', $translatedString);
  }

  public function testCanCheckLanguageIsValid()
  {
    $language1 = $this->polyglotLang->isValid('en');
    $language2 = $this->polyglotLang->isValid('rg');

    $this->assertTrue($language1);
    $this->assertFalse($language2);
  }

  public function testCanGetLocaleFromUrl()
  {
    $locale = $this->polyglotLang->getLocaleFromUrl();
    $this->assertEquals('fr', $locale);

    $request = $this->mockRequest();
    $request->shouldReceive('segment')->with(1)->andReturn('ds');
    $this->app['request'] = $request;

    $lang = new Polyglot\Language($this->app);
    $this->assertEquals('fr', $lang->getLocaleFromUrl());
  }

  public function testCanGetRoutesPrefix()
  {
    $prefix = $this->polyglotLang->getRoutesPrefix(array('before' => 'auth'));
    $this->assertEquals(array('before' => 'auth'), $prefix);

    $this->app['request'] = clone $this->mockRequest('en');
    $lang = new Polyglot\Language($this->app);
    $this->assertEquals(array('before' => 'auth', 'prefix' => 'en'), $lang->getRoutesPrefix(array('before' => 'auth')));
  }
}
