# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- N/A
### Changed
- N/A
### Removed
- N/A
## [v0.2.1] - 2020-04-08
### Added
- Allow ability to eager load
- Add optional getEagerSafe() macro on \Illuminate\Database\Eloquent\Builder class to automatically convert RelationNotFoundException to ValidationException
### Changed
- Modify Filter class and check for parameter naming collision.
### Removed
- N/A
## [v0.2.0] - 2020-06-14
### Added
- Allow empty query string
- Add more tests
### Changed
- Refactor code base
### Removed
- N/A
## [v0.1.4] - 2020-06-07
### Added
- Binding for related model's attribute
### Changed
- Refactor base Filter class
### Removed
- N/A
## [v0.1.3] - 2020-06-05
### Added
- Support for comma-delimited multi-valued GET params.
### Changed
- N/A
### Removed
- N/A
## [v0.1.2] - 2020-06-04
### Added
- Support for multi-valued GET params.
- `getDefaultWhereBuilder()` method.
### Changed
- Base Filter class.
### Removed
- N/A
## [v0.1.1] - 2020-05-18
### Added
- N/A
### Changed
- N/A
### Removed
- `get()` call from `filtered()` method to allow query chain
## [v0.1.0] - 2020-01-21
### Added
- The funnel:filter command
- Support for where, groupBy and orderBy clause
### Changed
- N/A
### Removed
- N/A
