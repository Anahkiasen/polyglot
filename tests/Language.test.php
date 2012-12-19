<?php
include '_start.php';

use Polyglot\Language;

class LanguageTest extends PolyglotTests
{
  public function testCanGetCurrentLanguage()
  {
    $current = Config::get('application.language');

    $this->assertEquals($current, Language::current());
  }

  public function testCanChangeLanguage()
  {
    Language::set('en');
    $current = Config::get('application.language');

    $this->assertEquals('en', $current);
  }

  public function testCanSetLocaleFromLanguage()
  {
    $locale = Language::locale('en');
    $translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

    $this->assertContains($locale, array('en_US.UTF8', 'en_US'));
    $this->assertEquals('January', $translatedString);
  }

  public function testCanCheckLanguageIsValid()
  {
    $language1 = Language::valid('en');
    $language2 = Language::valid('rg');

    $this->assertEquals(true, $language1);
    $this->assertEquals(false, $language2);
  }

  public function testCanCreateAnUrlToLanguage()
  {
    $link = Language::to('fr');

    $this->assertEquals('http://test/fr/', $link);
  }

  public function testCannotCreateUrlToInvalidLanguage()
  {
    $link = Language::to('sp');

    $this->assertEquals('http://test/en/', $link);
  }
}
