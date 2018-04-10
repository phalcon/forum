All notable changes to this project regarding 3.x branch.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.4.1] - 2017-12-07
### Added
- Added support for additional language to highlight syntax

### Changed
- Don't use session if session not started
- Improved notifications settings

### Fixed
- Notifications fixes
- Fixed double escaped title
- Fixed memcached session config

## [3.4.0] - 2017-11-29
### Added
- Add support of `<del>` and `<ins>` tags

### Changed
- Changed view render from `kzykhys/ciconia` to `erusev/parsedown`
- Moved `logs` and `cache` directories to the common place (`storage/{cache,logs}`)
- Updated path helpers
- Moved `config` directory to the project root
- Minor improvements and cleanup

### Fixed
- Fixed special symbols in preview mode
- Fixed blockquote text block (editor)
- Fixed dependencies and composer config

### Deleted
Removed no longer needed `error_polyfill`

## [3.3.2] - 2017-11-03
### Fixed
- Fixed issue [#40](https://github.com/phalcon/forum/issues/40) (related to answers)

## [3.3.1] - 2017-11-01
### Changed
- Cleaned container helper function
- Amended tests

### Fixed
- Fixed issue [#40](https://github.com/phalcon/forum/issues/40)

## [3.3.0] - 2017-10-30
### Added
- Provided ability to customize social links
- Improved categories route
- Improved tests suites
- Added `Discord` integration
- Added media queries so that the forum tab navigation isn't lost on iPads in portrait mode
- Added `SearchEngine` task

### Changed
- Refactored meta tags
- Improved digest mail template
- Updated dependencies

### Fixed
- Fixed error handler configuration to show pretty error page only for debug mode
- Fixed `Karma` algorithm
- Code cleanup, style fixes and more minor code fixes

## [3.2.1] - 2017-01-26
### Added
- Introduced error handler
- Added ability to use Patreon button

### Changed
- Cleaned old code

## [3.2.0] - 2017-01-13
### Added
- Introduced `CLI Dispatcher`
- Introduced `CLI Router`
- Introduced `CLI OptionParser`
- Introduced `CLI Application`
- Introduced `CLI runner`
- Introduced `CliInputListener`
- Introduced `CLI tasks`:
  - `cache:clear`
  - `notifications:send`
  - `notifications:queue`
  - `robots:generate`
  - `seeder:populate`
  - `sitemap:generate`
  - `commands`
  - `help`
  - `version`
- Introduced `Posts Service`
- Introduced `EmailComponent`
- Introduced application version component
- Added `singleton` helper

### Changed
- Cleaned old code
- Improved forum stat (use rating instead of amount)
- Used stable `Codeception`
- Improved routing
- Improved `Travis CI` build
- Improved mail templates
- Refactored tests

### Fixed
- Minor fix for detecting debug mode

### Deprecated
- PHP < 5.6 are now fully deprecated

### Deleted
- Removed not needed constants

## [3.1.2] - 2016-12-07
### Fixed
- Fixed Pager issue (undefined pager variable for list posts)

## [3.1.1] - 2016-12-06
### Fixed
- Fixed invalid numbers of bounds parameters for the Paginator [#286](https://github.com/phalcon/forum/issues/286)
- Fixed issue related to escaping poll options twice

## [3.1.0] - 2016-12-05
### Added
- Introduced the `Events Listeners`
- Introduced the `Service Providers`
- Introduced the `Volt functions`
- Introduced the `Model Services`
- Added the error routes
- Added ability to cache config on production mode

### Changed
- Refactored the error views
- Tune up the static pages
- Improved config factory
- Improved the discussions ordering
- Follow standard naming conventions
- Amended the `.env` config
- Used stable dependencies (Composer)

### Deleted
- Removed old code

## [3.0.2] - 2016-12-04
### Added
- Added some path helpers
- Introduced cache factory
- Added "The most active users" stat

### Changed
- Properly escaping user input/output
- Used stable `Codeception`
- Amended tests
- Amended helpers

### Fixed
- Fixed `reCaptcha` check issue

## [3.0.1] - 2016-11-27
### Added
- Added `ReCaptcha` support

### Changed
- Refactored configs
- Refactored tests
- Store karma to the session
- Tune up some views

## [3.0.0] - 2016-09-02
### Added
- Added ability to stick/unstick posts
- Added `PostsRepliesHistory::findLast`
- Added `PostsHistory::findLast`
- Added `PostsReplies::getDifference`
- Added `Posts::getDifference`
- Added `AJAX HTTP filter`

### Changed
- Improved Volt set up
- Improved Help index
- Code cleanup
- Amended development configs
- Refactor post history (diff)
- Separated post views into partials

### Fixed
- Fixed `RepliesController::historyAction`
- Fixed `Posts::afterSave`
- Fixed Backup util
- Minor style fix
- Fixed CSRF check for subscription, voting, editing, user settings, etc
- Fixed tests

### Deprecated
- Phalcon < 3.0.0 are now fully deprecated

[3.4.1]: https://github.com/phalcon/forum/compare/v3.4.0...v3.4.1
[3.4.0]: https://github.com/phalcon/forum/compare/v3.3.2...v3.4.0
[3.3.2]: https://github.com/phalcon/forum/compare/v3.3.1...v3.3.2
[3.3.1]: https://github.com/phalcon/forum/compare/v3.3.0...v3.3.1
[3.3.0]: https://github.com/phalcon/forum/compare/v3.2.1...v3.3.0
[3.2.1]: https://github.com/phalcon/forum/compare/v3.2.0...v3.2.1
[3.2.0]: https://github.com/phalcon/forum/compare/v3.1.2...v3.2.0
[3.1.2]: https://github.com/phalcon/forum/compare/v3.1.1...v3.1.2
[3.1.1]: https://github.com/phalcon/forum/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/phalcon/forum/compare/v3.0.2...v3.1.0
[3.0.2]: https://github.com/phalcon/forum/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/phalcon/forum/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/phalcon/forum/compare/v2.3.0...v3.0.0
