<?php
namespace Polyglot\Services;

use Polyglot\TestCases\PolyglotTestCase;

class LangTest extends PolyglotTestCase
{
	public function testCanChangeLanguage()
	{
		$this->translator->setLocale('en');

		$this->assertEquals('en', $this->translator->getLocale());
	}

	public function testCanCheckCurrentLanguage()
	{
		$this->translator->setLocale('en');

		$this->assertTrue($this->translator->active('en'));
	}

	public function testCanGetFallbackLocale()
	{
		$this->config = $this->mockConfig()->shouldReceive('get')->with('polyglot::fallback')->andReturn('es')->mock();
		$this->assertEquals('es', $this->translator->fallbackLocale());

		$this->config = $this->mockConfig()->shouldReceive('get')->with('polyglot::fallback')->andReturn(null)->mock();
		$this->assertEquals('fr', $this->translator->fallbackLocale());
	}

	public function testCantSetUnexistingLocales()
	{
		$this->translator->setLocale('ds');

		$this->assertEquals('fr', $this->translator->getLocale());
	}

	public function testCanSetLocaleFromLanguage()
	{
		$locale = $this->translator->setInternalLocale('en');
		$translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));
		$matcher = strlen($locale) == 5 ? 'en_US' : 'C/en_US.UTF-8/C/C/C/C';

		$this->assertEquals($locale, $matcher);
		$this->assertEquals('January', $translatedString);
	}

	public function testCanSetLocaleFromCurrent()
	{
		$this->translator->setLocale('en');
		$locale = $this->translator->setInternalLocale();
		$translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));
		$matcher = strlen($locale) == 5 ? 'en_US' : 'C/en_US.UTF-8/C/C/C/C';

		$this->assertEquals($locale, $matcher);
		$this->assertEquals('January', $translatedString);
	}

	public function testCanCheckLanguageIsValid()
	{
		$language1 = $this->translator->valid('en');
		$language2 = $this->translator->valid('rg');

		$this->assertTrue($language1);
		$this->assertFalse($language2);
	}

	public function testCanGetLocale()
	{
		$this->translator->setLocale('en');

		$localized = array("test" => true);

		$this->app['translation.loader']->shouldReceive('load')->with('en', 'test', "*")->andReturn($localized);
		$this->app['translation.loader']->shouldReceive('load')->with('fr', 'test', "*")->andReturn($localized);

		$this->config = $this->mockConfig()->shouldReceive('get')->with('test')->andReturn(true)->mock();

		$this->config->shouldReceive('get')->with('polyglot::fallback')->andReturn(null);

		$locale = $this->translator->get('test');

		$this->assertEquals($locale, $localized);
	}

	public function testMissingTranslationFallbacks()
	{
		$this->translator->setLocale('en');

		$localized = array("test" => true);

		$this->app['translation.loader']->shouldReceive('load')->with('en', 'test', '*')->andReturn(array());
		$this->app['translation.loader']->shouldReceive('load')->with('fr', 'test', '*')->andReturn($localized);

		$this->config = $this->mockConfig()->shouldReceive('get')->with('test')->andReturn(true)->mock();

		$this->config->shouldReceive('get')->with('polyglot::fallback')->andReturn('fr');

		$locale = $this->translator->get('test');

		$this->assertEquals($locale, $localized);
	}
}
