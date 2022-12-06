# Change Log

## [Unreleased][unreleased]

## [0.4.1] - 2022-12-06
### Fixed
- Better error message for `DisableMethodCallInContextRule`

## [0.4.0] - 2022-11-18
### Changed
- Parameter option name for `GuzzleClientCallWithoutOptionRule` changed to array option names 

### Added
- Rule for check calling method in some context (`DisableMethodCallInContextRule`)

## [0.3.0] - 2022-08-17
### Changed
- `GuzzleClientCallWithoutOptionRule` Changed node to MethodCall

### Added
- Support for options retrieved from method with typehints

## [0.2.0] - 2022-08-12
### Changed
- `GuzzleClientWithoutTimeoutOptionRule` changed to generic `GuzzleClientCallWithoutOptionRule` 

### Added
- Support for PHP 8.2

## [0.1.0] - 2022-08-09
### Added
- Rule for checking timeout options in Guzzle Client
- Rule for checking input params in Tomaj Nette API console
- Rule for checking trait context 
- Phpstorm meta dynamic return types

[unreleased]: https://github.com/efabrica-team/phpstan-rules/compare/0.4.1...HEAD
[0.4.1]: https://github.com/efabrica-team/phpstan-rules/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/efabrica-team/phpstan-rules/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/efabrica-team/phpstan-rules/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/efabrica-team/phpstan-rules/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/efabrica-team/phpstan-rules/compare/324b03236bdd7e9c44520cf1f4b9c7265a182e6c...0.1.0
