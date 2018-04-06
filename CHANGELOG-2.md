All notable changes to this project regarding 2.x branch.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.3.0] - 2016-07-13
### Added
- Enable tests for PHP 7.0
- Introduced docker-compose.yml for development purposes
- Added Security service
- Added ability to configure Elasticsearch host and port

### Changed
- Small code cleanup
- Used latest 2.x jQuery

### Fixed
- Fixed Elasticsearch Indexer
- Fixed random-entries script
- Fixed BlockQuoteExtension regexp
- Fixed updating recommended posts

## [2.2.0] - 2016-06-15
### Added
- Introduced Forum version constant
 
### Changed
- Refacored database backup task
- Used Application version for assets cache key

### Fixed
- Fixed sticky footer

## [2.1.0] - 2016-05-29
### Fixed
- Fixed Github authentication

## [2.0.5] - 2016-03-26
### Added
- Added development config example

### Changed
- Redesign errors
- Improved environment config
- Redesigned user's settings

### Fixed
- Fixed mail layouts
- Amended microdata
- Fixed statistic
- Cleanup notifiction
- Fixed polls-related cache issue

### Deprecated
- PHP 5.4 are now fully deprecated

## [2.0.4] - 2016-02-14
### Added
- Introduced Application Bootstrap
- Introduced environment config
- Introduced ability to override Application config
- Introduced ability to configure cache Backend/Frontend

### Changed
- Improved `ErrorController`
- Improved tests
- Improved `.htrouter.php`

### Deleted
- Phosphorum does not support Phalcon 2.0.4

### Fixed
- Added missed help layout

## [2.0.3] - 2016-02-12
### Added
- Added support of octicons
- Added font OpenSans
- Introduced structured data markup
- Introduced `UsersController`
- Enabled statistic for Categories

### Changed
- Cleaned `DiscussionsController`
- Improved Markdown Mention Extension
- Removed old code
- HTML Improvements

### Fixed
- Fixed config example
- Fixed routing

## [2.0.2] - 2016-02-06
### Added
- Added tests for sitemap and `robots.txt`
- Added Robots controller

### Changed
- Improved sitemap, added `robots.txt` Nginx locations
- Improved UI (badges, statistic footer)
- Improved logging

### Fixed
- Fixed `BadgeBase::getNoBountyCategories`
- Fixed PSR warnings
- Fixed build for Phalcon `2.1.x`
- Fixed & cleanup notify sender
- Fixed & cleanup digest sender

## 2.0.1 - 2016-01-31
### Added
- First release with ability of creating Polls

[2.3.0]: https://github.com/phalcon/forum/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/phalcon/forum/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/phalcon/forum/compare/v2.0.5...v2.1.0
[2.0.5]: https://github.com/phalcon/forum/compare/v2.0.4...v2.0.5
[2.0.4]: https://github.com/phalcon/forum/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/phalcon/forum/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/phalcon/forum/compare/v2.0.1...v2.0.2
