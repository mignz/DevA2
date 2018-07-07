# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/mignz/DevA2/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/mignz/DevA2/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/mignz/DevA2/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/mignz/DevA2/compare/v1.0.0...v1.0.1
