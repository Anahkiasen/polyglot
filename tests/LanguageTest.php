<?php
class LanguageTest extends PolyglotTests
{
  public function testCanGetCurrentLanguage()
  {
    $current = $this->app['lang']->getLocale();

    $this->assertEquals($current, $this->polyglotLang->current());
  }

  public function testCanChangeLanguage()
  {
    $this->polyglotLang->set('en');
    $current = $this->app['lang']->getLocale();

    $this->assertEquals('en', $current);
  }

  public function testCanCheckCurrentLanguage()
  {
    $this->polyglotLang->set('en');

    $this->assertTrue($this->polyglotLang->active('en'));
  }

  public function testCantSetUnexistingLocales()
  {
    $this->assertFalse($this->polyglotLang->set('ds'));
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
}
