<?php

use Knuckles\Scribe\Extracting\Strategies;

return [
    'title'              => 'Product API',
    'description'        => 'A clean RESTful API for managing products, built with Laravel.',
    'base_url'           => env('APP_URL', 'http://localhost:8000'),

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains'  => ['*'],
                'versions' => ['v1'],
            ],
            'include' => [],
            'exclude' => [],
            'apply'   => [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'response_calls' => [
                    'methods' => ['GET'],
                    'config'  => [
                        'APP_ENV'   => 'documentation',
                        'APP_DEBUG' => false,
                    ],
                    'queryParams'  => [],
                    'bodyParams'   => [],
                    'fileParams'   => [],
                    'cookies'      => [],
                ],
            ],
        ],
    ],

    'type'    => 'static',
    'theme'   => 'default',
    'static'  => [
        'output_path' => 'public/docs',
    ],
    'laravel' => [
        'add_routes'    => true,
        'docs_url'      => '/docs',
        'middleware'    => [],
        // Directory within `public` in which to store CSS and JS assets.
        // By default, assets are stored in `public/vendor/scribe`.
        // If set, assets will be stored in `public/{{assets_directory}}`
        'assets_directory' => null,
    ],

    'auth' => [
        'enabled'     => true,
        'default'     => false,
        'in'          => 'bearer',
        'name'        => 'Authorization',
        'use_value'   => env('SCRIBE_AUTH_KEY'),
        'placeholder' => '{YOUR_AUTH_KEY}',
        'extra_info'  => 'Obtain a token via POST /api/v1/login. Pass it as: Authorization: Bearer {token}',
    ],

    'intro_text' => <<<'INTRO'
## Overview

This API provides full CRUD management for the **Product** resource, with optional Sanctum token authentication.

### Base URL
```
http://localhost:8000/api/v1
```

### Response format
All responses are JSON. Successful responses follow:
```json
{ "data": { ... } }          // single resource
{ "data": [...], "meta": {} } // collection with pagination
```

### Error format
```json
{ "message": "...", "errors": { "field": ["..."] } }
```

### Auth
Public endpoints: `GET /products`, `GET /products/{id}`
Protected endpoints (require Bearer token): `POST`, `PUT`, `DELETE`
INTRO,

    'example_languages' => ['bash', 'javascript'],
    'postman'           => ['enabled' => true, 'overrides' => []],
    'openapi'           => ['enabled' => true, 'overrides' => []],

    'groups' => [
        'default' => 'Endpoints',
        'order'   => [
            'Authentication',
            'Products',
        ],
    ],

    'strategies' => [
        'metadata'            => [Strategies\Metadata\GetFromDocBlocks::class],
        'urlParameters'       => [Strategies\UrlParameters\GetFromLaravelAPI::class, Strategies\UrlParameters\GetFromUrlParamTag::class],
        'queryParameters'     => [Strategies\QueryParameters\GetFromFormRequest::class, Strategies\QueryParameters\GetFromQueryParamTag::class],
        'headers'             => [Strategies\Headers\GetFromRouteRules::class, Strategies\Headers\GetFromHeaderTag::class],
        'bodyParameters'      => [Strategies\BodyParameters\GetFromFormRequest::class, Strategies\BodyParameters\GetFromBodyParamTag::class],
        'responses'           => [Strategies\Responses\UseResponseTag::class, Strategies\Responses\UseResponseFileTag::class, Strategies\Responses\UseApiResourceTags::class, Strategies\Responses\ResponseCalls::class],
        'responseFields'      => [Strategies\ResponseFields\GetFromResponseFieldTag::class],
    ],

    'fractal' => ['serializer' => null],
    'routeMatcher' => \Knuckles\Scribe\Matching\RouteMatcher::class,
    'external' => ['html_attributes' => []],
    'try_it_out' => [
        // Add a Try It Out button to your endpoints so consumers can test endpoints right from their browser.
        // Don't forget to enable CORS headers for your endpoints.
        'enabled' => true,
        // The base URL for the API tester to use (for example, you can set this to your staging URL).
        // Leave as null to use the current app URL when generating (config("app.url")).
        'base_url' => null,
        // [Laravel Sanctum] Fetch a CSRF token before each request, and add it as an X-XSRF-TOKEN header.
        'use_csrf' => false,
        // The URL to fetch the CSRF token from (if `use_csrf` is true).
        'csrf_url' => '/sanctum/csrf-cookie',
    ],
    // Custom logo path. This will be used as the value of the src attribute for the <img> tag,
    // so make sure it points to an accessible URL or path. Set to false to not use a logo.
    // For example, if your logo is in public/img:
    // - 'logo' => '../img/logo.png' // for `static` type (output folder is public/docs)
    // - 'logo' => 'img/logo.png' // for `laravel` type
    'logo' => false,
    // Customize the "Last updated" value displayed in the docs by specifying tokens and formats.
    // Examples:
    // - {date:F j Y} => March 28, 2022
    // - {git:short} => Short hash of the last Git commit
    // Available tokens are `{date:<format>}` and `{git:<format>}`.
    // The format you pass to `date` will be passed to PHP's `date()` function.
    // The format you pass to `git` can be either "short" or "long".
    'last_updated' => 'Last updated: {date:F j, Y}',
    'examples' => [
        // Set this to any number (e.g. 1234) to generate the same example values for parameters on each run,
        'faker_seed' => null,
        // With API resources and transformers, Scribe tries to generate example models to use in your API responses.
        // By default, Scribe will try the model's factory, and if that fails, try fetching the first from the database.
        // You can reorder or remove strategies here.
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],
];
