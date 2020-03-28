# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.1] - 2020-03-28
### New
- Added a Docker HEALTHCHECK instruction
### Changed
- Disabled Xdebug remote by default to improve PHP performance
### Fixed
- Phalcon upgrade script was not working
- Duplicated Xdebug extension ini file
- Nginx logs were not completely disabled

## [1.3.0] - 2020-03-23
### New
- Added PSR PHP extension
### Changed
- Version update: PHP version 7.3.11 —> 7.4.3
- Version update: Phalcon version 3.4.5 —> 4.0.5
- Temporarily removed Imagick PHP extension

## [1.2.4] - 2019-12-07
### Changed
- Version update: MariaDB version 10.3.18 —> 10.3.20
- Version update: PHP version 7.3.10 —> 7.3.11
- Version update: Phalcon version 3.4.4 —> 3.4.5
### Fixed
- Phalcon was upgrading to non-stable versions

## [1.2.3] - 2019-10-23
### Added
- Instructions on how to add certificates to macOS keychain
### Changed
- Version update: MariaDB version 10.3.17 —> 10.3.18
- Version update: PHP version 7.3.8 —> 7.3.10
- Https -> http on the cp.test link in localhost

## [1.2.2] - 2019-08-15
### Changed
- Version update: Alpine version 3.9 —> 3.10
- Version update: Nginx version 1.14.2 —> 1.16.1
- Version update: MariaDB version 10.3.12 —> 10.3.17
- Version update: Phalcon version 3.4.2 —> 3.4.4
- Version update: PHP version 7.2.x —> 7.3.8
- Version update: Redis version 4.0.12 —> 5.0.5
- Travis now checks for a MySQL connection
- Default PHP timezone is Europe/Lisbon
### Fixed
- CP was checking for Phalcon beta versions

## [1.2.1] - 2019-02-23
### Changed
- Version update: Alpine version 3.8 —> 3.9
- Version update: Nginx version 1.14.0 —> 1.14.2
- Version update: MariaDB version 10.2.15 —> 10.3.12
- Version update: Phalcon version 3.4.1 —> 3.4.2
- Version update: Redis version 4.0.11 —> 4.0.12

## [1.2.0] - 2018-09-20
### Added
- Option to select timezone for PHP
- Option to select what to backup
- Each domain has it's own certificate (with SAN)
### Changed
- Supervisord replaced with s6
- Improved PHP-FPM performance
- Minor UI improvements to the CP

## [1.1.2] - 2018-09-01
### Added
- Link to UPGRADE.md and CHANGELOG.md in CP
### Changed
- Version update: Phalcon version 3.4.0 —> 3.4.1
- Version update: Redis version 4.0.10 —> 4.0.11

## [1.1.1] - 2018-07-11
### Fixed
- Restore was not importing MySQL databases

## [1.1.0] - 2018-07-06
### Added
- Enabled PHP's short open tag
### Changed
- Completely restructured the backup/restore logic and improved UI
- Version update: Alpine Linux version 3.7 —> 3.8
- Version update: Nginx version 1.12.2 —> 1.14.0
- Version update: MariaDB version 10.1.32 —> 12.2.15
- Version update: Redis version 4.0.6 —> 4.0.10
- Reduced the size of the Docker image
### Fixed
- Increased the PHP max execution time to avoid timeouts when creating a backup

## [1.0.2] - 2018-06-20
### Added
- Added STARTTLS to the SSMTP configuration
### Fixed
- Increased the Nginx time limit when creating a backup
- The "mysql" database was being included in a MySQL dump
- Backup was creating a corrupt file
- Nginx was creating a huge log after being restarted through the CP

## [1.0.1] - 2018-06-19
### Added
- Enabled Xdebug remote
- Improved the control panel UI
### Changed
- Improved readme

[Unreleased]: https://github.com/mignz/DevA2/compare/v1.3.1...HEAD
[1.3.1]: https://github.com/mignz/DevA2/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/mignz/DevA2/compare/v1.2.4...v1.3.0
[1.2.4]: https://github.com/mignz/DevA2/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/mignz/DevA2/compare/v1.2.2...v1.2.3
[1.2.2]: https://github.com/mignz/DevA2/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/mignz/DevA2/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/mignz/DevA2/compare/v1.1.2...v1.2.0
[1.1.2]: https://github.com/mignz/DevA2/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/mignz/DevA2/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/mignz/DevA2/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/mignz/DevA2/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/mignz/DevA2/compare/v1.0.0...v1.0.1
