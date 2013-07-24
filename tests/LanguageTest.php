<?php
class LanguageTest extends PolyglotTests
{
  public function testCanGetCurrentLanguage()
  {
    $current = $this->app['lang']->getLocale();

    $this->assertEquals($current, $this->app['polyglot.lang']->current());
  }

  public function testCanChangeLanguage()
  {
    $this->app['polyglot.lang']->set('en');
    $current = $this->app['lang']->getLocale();

    $this->assertEquals('en', $current);
  }

  public function testCanSetLocaleFromLanguage()
  {
    $locale = $this->app['polyglot.lang']->locale('en');
    $translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

    $this->assertContains($locale, array('en_US.UTF8', 'en_US'));
    $this->assertEquals('January', $translatedString);
  }

  public function testCanCheckLanguageIsValid()
  {
    $language1 = $this->app['polyglot.lang']->isValid('en');
    $language2 = $this->app['polyglot.lang']->isValid('rg');

    $this->assertTrue($language1);
    $this->assertFalse($language2);
  }
}
