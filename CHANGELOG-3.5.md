All notable changes to this project regarding 3.5.x branch.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.5.0] - 2018-28-04
### Added
- Add pagination when category was selected [#371](https://github.com/phalcon/forum/issues/371)
- Add changelog [#58](https://github.com/phalcon/forum/issues/58)
- Add `UsersListener` to `Users` model [#465](https://github.com/phalcon/forum/issues/465)
- Added support syntax highlight in preview
- Added Post listener
- Added Assets Collection feature and do not use CDN anymore
- Add mailer and change notification task

### Changed
- Change replies counter. Amount include posts's owner and visitors' replies [#50](https://github.com/phalcon/forum/issues/50)
- Cleaned Posts model

### Fixed
- Prevent throwing error when queue does not work
- Fixed post views logic to prevent increase views by post author
- Fixed Mentions Plugin to allow user names with dash and underline
- Fixed incorrect user name's link when it has dash


[3.5.0]: https://github.com/phalcon/forum/compare/v3.4.1...v3.5.0
