# About this Package
This package is the brains behind the Autotask API client. It generates each service class using the Autotask API as its guide.

## To Install
Run `composer require --dev anteris-dev/autotask-client-generator`

# Commands
This package provides command line commands. These are listed below.

## Make Endpoint
Running `vendor/bin/autotask make:endpoint <entity>` will create new endpoint classes for `<entity>`. `<entity>` should be the singular or plural form of an Autotask endpoint (e.g. "Ticket" or "Tickets" respectively).

You may pass the argument `--output=<directory>` to set the directory to write these classes in.

You may pass the argument `--force` to overwrite existing classes.

## Make Defaults
Running `vendor/bin/autotask make:defaults` will create new endpoint classes for all known Autotask endpoints.

You may pass the argument `--output=<directory>` to set the directory to write these classes in.

You may pass the argument `--force` to overwrite existing classes.

## Make Support Files
Running `vendor/bin/autotask make:support-files` will generate support classes that are used across multiple domains. Examples of these classes are http clients, entity classes, etc.

You may pass the argument `--output=<directory>` to set the directory to write these classes in.

You may pass the argument `--force` to overwrite existing classes.

# Directory Structure

- /bin - _Contains the command line logic for the `autotask` command._
- /docs - _Contains documentation surrounding this package._
- /src - _Contains the generator source code._
  - /Command - _Contains any Symfony commands that the `autotask` command supports._
  - /DataTransferObject - _Contains any DTOs that are used in handling data during the generator process._
  - /Helper - _Contains static function helpers that are used throughout the package._
- /templates - _Contains Twig templates for the generated package._
  - /Package - _These templates are related to the actual package._
  - /Tests - _These templates are related to the package tests._
