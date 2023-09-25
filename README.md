# MorningMedley - Blocks

A MorningMedley service for WordPress blocks.

## Introduction

This tool is made for organizing WordPress Gutenberg blocks!

This service lets you:

- Load all blocks found in a directory
- Render Blade views by defining them as `renderView` in block meta
- Load PHP dependencies by adding them to `phpScript`, `editorPhpScript` and `viewPhpScript` in `block.json`

## Getting Started

To get started install the package as described below in [Installation](#installation).

Look at [Usage](#usage) to learn how to, well, use it.

### Installation

Install with composer

```bash
composer require morningmedley/blocks
```

## Dependencies

###    

- PHP 8.0 or greater
- [Symfony Finder](https://symfony.com/doc/current/components/finder.html) is used to locate blocks and then register
  them
- [symfony/cache](https://symfony.com/doc/current/components/cache.html) is used to cache the found files and their
  dependencies in production for better performance
- [illuminate/container](https://github.com/illuminate/container) is the container wrapping the service
- [illuminate/support](https://github.com/illuminate/support) is used for Facades and for Collections

## Usage

Add the paths containing blocks to the application config and make sure that the service has been registered as a
service.

```php
<?php return [
    'app' => [
        'providers' => [
            'MorningMedley\\Blocks\\ServiceProvider',
        ],
    ],
    'wp-blocks' => [
        'path' => [
            'public/build/blocks',
        ],
    ],
];
```

**Note:** `public/build/blocks` is the default path.

## Using a View

To serverside render a block using a Blade View set the custom `renderView` property.

You can also have custom PHP code dependency. By declaring `phpScript` the given script will be loaded alongside the
registration of your block.
This is especially useful when needing a View Composer.

Note the custom schema url!

```json
{
    "$schema": "https://wp.cdn.mtra.in/default/schemas/block.json",
    "apiVersion": 3,
    "name": "foo/bar",
    "version": "0.1.0",
    "title": "Bar",
    "textdomain": "foo",
    "editorScript": "file:./index.js",
    "editorStyle": "file:./index.css",
    "style": "file:./style-index.css",
    "renderView": "my-view",
    "phpScript": "file:./script.php",
    "viewPhpScript": [
        "file:./view-script.php",
        "file:./view-script2.php"
    ],
    "editorPhpScript": "file:./editor-script.php"
}
```

The view will have the following vars: `$block`, `$attributes`, `$content` and `$blockProps`

Example:

```
<div {!! $blockProps !!}>
    <h2>{{$attributes['title']}}</h2>
    <div>{!! $content !!}</div>
</div>
```

If you wish to view compose you may create a `*.php` file within your block folder.
As long as it is a sibling to the `block.json` file and is not named `*.asset.php` then it will automatically be loaded.

## Caching

If your environment is `production` then a cache containing all block files and their dependencies will be generated and
used so that the server doesn't have to look for them on every request.

To clear this cache you can use the CLI command:

```sh
wp wp-blocks deleteCache
```

## Credits

- [Mathias Munk](https://github.com/mrmoeg)
- [All Contributors](../../contributors)

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
