# lum.mailer.php

## Summary

A Mailer library that by default wraps [Symfony Mailer] and adds
some functionality that is mostly specific to the [Lum App Framework].

This is meant as a modular component of the Lum Framework to provide a
standardized API for any kind of action that requires email messaging.

For a lot of more generic email uses, you can simply use Symfony Mailer
directly rather than using this wrapper library.

## v3 Notes

This version is **NOT** backwards compatible with any prior versions.
It has changed the classnames, namespaces, and defaults.

It has dropped the old `SendGrid` transport plugin. 
If you need to use SendGrid, you should install `symfony/sendgrid-mailer` 
and then set the `dsn` option to `sendgrid://APIKEY@default`.

Many option names have changed as well, read the docs for details.

## Classes

| Class                           | Description                               |
| ------------------------------- | ----------------------------------------- |
| Lum\Mailer\Manager              | The main manager component used by apps.  |
| Lum\Mailer\Templates\Plugin     | Interface for template plugins.           |
| Lum\Mailer\Templates\Symfony    | Templates via Symfony (uses Twig).        |
| Lum\Mailer\Templates\TextList   | Plain text messages (no template).        |
| Lum\Mailer\Templates\ViewLoader | Templates using a [Lum Core] view loader. |
| Lum\Mailer\Transport\Symfony    | The Symfony Mailer transport plugin.      |
| Lum\Mailer\Transport\Plugin     | An interface for transports.              |

### Plugin notes

- If no `transport` option is specified, `Symfony` will be used.
  This is currently the _only_ transport included by default.
  Custom transports may be created and specified for special use cases.
- If no `templates` option is specified, `ViewLoader` will be used.
  In the future the default _MAY_ change to `Symfony` but for now
  it's sticking with the templates as used by version 2.x and earlier.
- If `templates` is `Symfony` but `transport` is NOT `Symfony`,
  an Exception will be thrown. The Symfony template engine MUST only
  be used in conjunction with the Symfony transport!

## Official URLs

This library can be found in two places:

 * [Github](https://github.com/supernovus/lum.mailer.php)
 * [Packagist](https://packagist.org/packages/lum/lum-mailer)

## Author

Timothy Totten

## License

[MIT](https://spdx.org/licenses/MIT.html)

---

[Lum App Framework]: https://github.com/supernovus/lum.app.php
[Lum Core]: https://github.com/supernovus/lum.core.php
[Symfony Mailer]: https://symfony.com/doc/current/mailer.html
