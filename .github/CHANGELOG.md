# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Folder structure of Twig templates to reflect that of the generated package.
- Filenames of Twig templates to end with the extension of `.php.twig`.


## [v0.2.0]

### Added
- `count()` method on QueryBuilder classes.
- `getEntityFields()` method on service classes.
- `getEntityInformation()` method on service classes.
- `getEntityUserDefinedFields()` method on service classes that support UDFs.

### Changed
- Service classes return User Defined Fields as an array of UDF entities.

## [v0.1.0]

### Added
- Initial generator files

[Unreleased]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v0.2.0...HEAD
[v0.2.0]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v0.1.0...v0.2.0
[v0.1.0]: https://github.com/Anteris-Dev/autotask-client-generator/releases/tag/v0.1.0
