# Changelog

## 1.3.1

- Fix recursive finding of translatable files in app/ directory

## 1.3.0

- Fix a bug in Url::locale
- Bump minimum requirements to 5.4+

## 1.2.1

- Touch the parent of a lang entity when the latter is changed
- Prevent refetching of attributes during joins
- Fix a bug where the fallback locale wasn't properly used

## 1.2.0

- Added a `fallback` language option
- Added `URL::language` to get an URL to a language
- Added `URL::switchLanguage` to get an URL to the current page in another language
- Dynamic language methods

## 1.1.0

- `Lang::get` now loads translations in your default locale if none found in the current one
- Fix deferred service providers bugs

## 1.0.0

- Rewrite Polyglot to use the existing Facades (see README)

## 0.1.0

- Initial release
