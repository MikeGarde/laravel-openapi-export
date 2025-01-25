# Laravel OpenAPI Export

This package provides a simple way to export your Laravel routes to an OpenAPI 3.0 specification file.

## Installation

You can install the package via composer:

```bash
composer require mikegarde/laravel-openapi-export --dev
```

## Usage

To export your routes to an OpenAPI 3.0 specification file, run the following command:

```bash
php artisan openapi:export
```

This will generate a file called `openapi.yml` in the root of your project. Pop it into your favorite OpenAPI viewer like [editor.swagger.io](https://editor.swagger.io/) and you're 4% closer to having a fully documented API!

### Options

Change the output file path:

```bash
php artisan openapi:export --output=path/to/docs/file.yml
```

Change the output format:

```bash
php artisan openapi:export --format=json
```

## Raw Output

```bash
php artisan openapi:export --raw
```

In RESTful API design, route parameters are typically used to identify resources or perform specific actions. They are generally required, as their presence directly impacts the resource being accessed or manipulated. However, Laravel allows for optional route parameters in URLs (e.g., /users/{id?}), which can lead to ambiguity and inconsistencies in how the API behaves or is interpreted by developers and consumers.

By default, this package resolves the ambiguity introduced by optional route parameters by duplicating such routes into two distinct versions:

 - A route where the optional parameter is entirely absent.
 - A route where the parameter is included but marked as required.

This approach aligns with API best practices, where route parameters in the path are considered mandatory for proper identification of resources. It ensures that:

 - Clarity: Consumers of the API know understand the different responses when "optional" parameters are skipped.
 - Consistency: The API adheres to conventional REST principles, avoiding unexpected behavior when optional parameters are included in a path.
 - Validation: APIs that depend on OpenAPI/Swagger documentation or automated tools can properly validate requests, reducing errors caused by ambiguous routing definitions.

However if you want to keep the optional parameters as optional, you can disable this behavior by setting the `--raw` flag when running the `openapi:export` command.

## TODO

- [ ] Add support for exporting only specific routes
- [ ] Add support for exporting only routes with specific tags
- [ ] Add support for exporting only routes with specific methods
- [ ] Add support for updating existing OpenAPI files
