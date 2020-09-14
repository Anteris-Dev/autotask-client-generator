# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- HttpClientFactory for creating Guzzle http clients

### Changed
- Paginator, QueryBuilder, and Service classes to use the Guzzle client
- Method by which Guzzle requests are sent and responses are received
- Way that entities are built from Guzzle responses

## [v1.0.3] - 2020-09-08

### Added
- Support for vlucas/phpdotenv 5.0 which was required by Illuminate Support

## [v1.0.2] - 2020-09-08

### Added
- Support for Guzzle 7.0 and Illuminate Support 8.0

## [v1.0.1] - 2020-09-01

### Fixed
- References to the QueryBuilder in the Service class doc block.

## [v1.0.0] - 2020-09-01

### Added
- FileWriter classes which handle any interaction with files for this package.
- Generator classes which handle the generation of any files within their domain.
- Response entities and collections to easily transfer information between generators.
- Caching mechanism for Autotask API responses.
- __toString() method on the QueryBuilder template which allows the built query to be used as a string.
- Snake case forms of the entity name to the `EntityNameDTO` for unit tests.
- GLCode, MSRP, SGDA, SIC, and SKU to weird words that are lowercased instead of camel cased.
- Basic unit test generation for the service classes. Currently tests: that client returns correct service class, that querying returns the right collection, that collections contain the right entities, and that the `query()` method returns the right query builder. **Requires more work!**
- `loop()` method to QueryBuilder.

### Changed
- Folder structure of Twig templates to reflect that of the generated package.
- Filenames of Twig templates to end with the extension of `.php.twig`.
- Changed the name of the entity name data transfer object and moved it to the support folder.
- `paymentTerms` and `quantityNowReceiving` are now nullable given how Autotask responds to these requests.
- Types of _long_ and _short_ from Autotask are given no type. There is not a good PHP alternative (int is too short, double does not work).
- ContractID is cast to an integer instead of string. (Autotask says its dataType should be string but returns int).
- Directory structure to plural form of name as with the generated files.

### Fixed
- Paginator classes were generated with a funky $contacts variable (even if they were not a contact resource!)

### Removed
- Docs directory. This belongs elsewhere.

## [v0.2.0] - 2020-08-25

### Added
- `count()` method on QueryBuilder classes.
- `getEntityFields()` method on service classes.
- `getEntityInformation()` method on service classes.
- `getEntityUserDefinedFields()` method on service classes that support UDFs.

### Changed
- Service classes return User Defined Fields as an array of UDF entities.

## [v0.1.0] - 2020-08-25

### Added
- Initial generator files

[v1.0.3]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v1.0.2...v1.0.3
[v1.0.2]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v1.0.1...v1.0.2
[v1.0.1]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v0.2.0...v1.0.0
[v0.2.0]: https://github.com/Anteris-Dev/autotask-client-generator/compare/v0.1.0...v0.2.0
[v0.1.0]: https://github.com/Anteris-Dev/autotask-client-generator/releases/tag/v0.1.0
