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

You may pass the argument `--no-cache` to redownload the entity information from Autotask.

## Make Defaults
Running `vendor/bin/autotask make:defaults` will create new endpoint classes for all known Autotask endpoints.

You may pass the argument `--output=<directory>` to set the directory to write these classes in.

You may pass the argument `--force` to overwrite existing classes.

You may pass the argument `--no-cache` to redownload the entity information from Autotask.

## Make Support Files
Running `vendor/bin/autotask make:support-files` will generate support classes that are used across multiple domains. Examples of these classes are http clients, entity classes, etc.

You may pass the argument `--output=<directory>` to set the directory to write these classes in.

You may pass the argument `--force` to overwrite existing classes.

# Directory Structure

- /bin - _Contains the command line logic for the `autotask` command._
- /src - _Contains the generator source code._
  - /Commands - _Contains any Symfony commands that the `autotask` command supports._
  - /Generators - _Contains classes concerned with converting entity information into an actual class._
  - /Helpers - _Contains static function helpers that are used throughout the package._
  - /Responses - _Contains data transfer objects for storing the entity information responses from Autotask._
  - /Support - _Contains any classes used across multiple domains._
  - /Writers - _Contains classes concerned with writing strings to files._
  - Generator.php - _The main generator class. In charge of everything._
- /templates - _Contains Twig templates for the generated package._
  - /Package - _These templates are related to the actual package._
  - /Tests - _These templates are related to the package tests._
