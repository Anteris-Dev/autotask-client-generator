# About this Package
This package is the brains behind the Autotask API client. It generates each service class using the Autotask API as its guide.

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
