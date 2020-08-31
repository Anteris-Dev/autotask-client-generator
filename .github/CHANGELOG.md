# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- FileWriter classes which handle any interaction with files for this package.
- Generator classes which handle the generation of any files within their domain.
- Response entities and collections to easily transfer information between generators.
- Caching mechanism for Autotask API responses.
- __toString() method on the QueryBuilder template which allows the built query to be used as a string.

### Changed
- Folder structure of Twig templates to reflect that of the generated package.
- Filenames of Twig templates to end with the extension of `.php.twig`.
- Changed the name of the entity name data transfer object and moved it to the support folder.

### Fixed
- Paginator classes were generated with a funky $contacts variable (even if they were not a contact resource!)

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
