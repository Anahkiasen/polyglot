# CHANGELOG

## 1.3.2 - 2014-11-17

- Fixed out of date Twig clear command call

## 1.3.1 - 2014-08-19

### Fixed
- Fix recursive finding of translatable files in app/ directory

## 1.3.0 - 2014-08-05

### Changed
- Bump minimum requirements to 5.4+

### Fixed
- Fix a bug in Url::locale

## 1.2.1 - 2014-06-08

### Changed
- Touch the parent of a lang entity when the latter is changed

### Fixed
- Prevent refetching of attributes during joins
- Fix a bug where the fallback locale wasn't properly used

## 1.2.0 - 2014-02-08

### Added
- Added a `fallback` language option
- Added `URL::language` to get an URL to a language
- Added `URL::switchLanguage` to get an URL to the current page in another language
- Dynamic language methods

## 1.1.0 - 2013-08-08

### Added
- `Lang::get` now loads translations in your default locale if none found in the current one

### Fixed
- Fix deferred service providers bugs

## 1.0.0 - 2013-08-08

### Changed
- Rewrite Polyglot to use the existing Facades (see README)
