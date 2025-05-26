# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.6.0] - 2025-05-25
### Changed
- Removed PHP pspell extension
- Version update: Alpine version 3.17 -> 3.21
- Version update: Nginx version 1.22.1 -> 1.26.3
- Version update: MariaDB version 10.6.11 -> 11.4.5
- Version update: Phalcon version 5.1.3 —> 5.9.3
- Version update: PHP version 8.1.13 -> 8.4.5
- Version update: Redis version 7.0.7 -> 7.2.8

## [1.5.3] - 2023-01-01
### Changed
- Version update: Alpine version 3.16 -> 3.17
- Version update: Nginx version 1.22.0 -> 1.22.1
- Version update: MariaDB version 10.6.9 -> 10.6.11
- Version update: Phalcon version 5.0.0RC4 —> 5.1.3
- Version update: PHP version 8.1.9 -> 8.1.13
- Version update: Redis version 7.0.4 -> 7.0.7

## [1.5.2] - 2022-09-07
### Changed
- Version update: Alpine version 3.15 -> 3.16
- Version update: Nginx version 1.20.2 -> 1.22.0
- Version update: MariaDB version 10.6.7 -> 10.6.9
- Version update: Phalcon version 5.0.0beta3 —> 5.0.0RC4
- Version update: PHP version 8.0.16 -> 8.1.9
- Version update: Redis version 6.2.6 -> 7.0.4

## [1.5.1] - 2022-03-18
### Changed
- Added PHP libsodium extension
- Version update: Alpine version 3.14 -> 3.15
- Version update: Nginx version 1.20.1 -> 1.20.2
- Version update: MariaDB version 10.5.12 -> 10.6.7
- Version update: Phalcon version 5.0.0alpha6 —> 5.0.0beta3
- Version update: PHP version 8.0.10 -> 8.0.16
- Version update: Redis version 6.2.5 -> 6.2.6

## [1.5.0] - 2021-09-19
### Changed
- Version update: Alpine version 3.13 -> 3.14
- Version update: Nginx version 1.18.0 —> 1.20.1
- Version update: MariaDB version 10.5.9 —> 10.5.12
- Version update: Phalcon version 4.1.2 —> 5.0.0alpha6
- Version update: PHP version 7.4.15 —> 8.0.10
- Version update: Redis version 6.0.11 —> 6.2.5

## [1.4.2] - 2021-05-01
### Changed
- Version update: MariaDB version 10.5.8 —> 10.5.9
- Version update: Phalcon version 4.1.0 —> 4.1.2
- Version update: PHP version 7.4.14 —> 7.4.15
- Version update: Redis version 6.0.10 —> 6.0.11

## [1.4.1] - 2021-01-17
### Changed
- Fixed MySQL connection not working on the host machine

## [1.4.0] - 2021-01-15
### Changed
- Added support for multiple architectures
- Version update: Alpine version 3.11 -> 3.13
- Version update: MariaDB version 10.4.13 —> 10.5.8
- Version update: PHP version 7.4.5 —> 7.4.14
- Version update: Phalcon version 4.0.6 —> 4.1.0
- Version update: Redis version 5.0.7 —> 6.0.10

## [1.3.2] - 2020-05-31
### Changed
- Version update: Alpine version 3.10 -> 3.11
- Version update: PHP version 7.4.3 —> 7.4.5
- Version update: Phalcon version 4.0.5 —> 4.0.6
- Version update: Redis version 5.0.5 —> 5.0.7
- Version update: MariaDB version 10.3.20 —> 10.4.13

## [1.3.1] - 2020-03-29
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

[Unreleased]: https://github.com/mignz/DevA2/compare/v1.6.0...HEAD
[1.6.0]: https://github.com/mignz/DevA2/compare/v1.5.3...v1.6.0
[1.5.3]: https://github.com/mignz/DevA2/compare/v1.5.2...v1.5.3
[1.5.2]: https://github.com/mignz/DevA2/compare/v1.5.1...v1.5.2
[1.5.1]: https://github.com/mignz/DevA2/compare/v1.5.0...v1.5.1
[1.5.0]: https://github.com/mignz/DevA2/compare/v1.4.2...v1.5.0
[1.4.2]: https://github.com/mignz/DevA2/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/mignz/DevA2/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/mignz/DevA2/compare/v1.3.2...v1.4.0
[1.3.2]: https://github.com/mignz/DevA2/compare/v1.3.1...v1.3.2
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
