<?php
class LangTest extends PolyglotTests
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

	public function testCantSetUnexistingLocales()
	{
		$this->translator->setLocale('ds');

		$this->assertEquals('fr', $this->translator->getLocale());
	}

	public function testCanSetLocaleFromLanguage()
	{
		$locale = $this->translator->setInternalLocale('en');
		$translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

		$this->assertEquals($locale, 'en_US');
		$this->assertEquals('January', $translatedString);
	}

	public function testCanSetLocaleFromCurrent()
	{
		$this->translator->setLocale('en');
		$locale = $this->translator->setInternalLocale();
		$translatedString = strftime('%B', mktime(0, 0, 0, 1, 1, 2012));

		$this->assertEquals($locale, 'en_US');
		$this->assertEquals('January', $translatedString);
	}

	public function testCanCheckLanguageIsValid()
	{
		$language1 = $this->translator->valid('en');
		$language2 = $this->translator->valid('rg');

		$this->assertTrue($language1);
		$this->assertFalse($language2);
	}
}
