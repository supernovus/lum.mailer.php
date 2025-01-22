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

It has dropped the old `SendGrid` plugin. If you need to use SendGrid,
you should install the `symfony/sendgrid-mailer` plugin and then set your 
mail DSN string to `sendgrid://APIKEY@default`.

Many option names have changed as well, read the docs for details.

## Classes

| Class  | Description                                                        |
| ------ | ------------------------------------------------------------------ |
| Lum\Mailer\Manager | The main manager component used by apps.               |
| Lum\Mailer\Templates\TemplateInterface | Interface for template plugins.    |
| Lum\Mailer\Templates\ViewLoader | Templates using a [Lum Core] view loader. |
| Lum\Mailer\Transport\Symfony | The Symfony Mailer transport plugin.         |
| Lum\Mailer\Transport\TransportInterface | An interface for transports.      |

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
