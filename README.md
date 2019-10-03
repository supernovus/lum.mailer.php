# lum.mailer.php

## Summary

A Mailer library that can wrap either SwiftMailer, or SendGrid.

To use it with SwiftMailer, include `swiftmailer/swiftmailer` in your
`require` dependency list.

To use it with SendGrid, include `sendgrid/sendgrid` in your `require` list.

## Classes

| Class                   | Description                                       |
| ----------------------- | ------------------------------------------------- |
| Lum\Mailer              | The Core Mailer front-end library.                |
| Lum\Mailer\Swift        | The SwiftMailer transport plugin.                 |
| Lum\Mailer\SendGrid     | The SendGrid transport plugin.                    |

## TODO

Update the `SendGrid` plugin from using the `sendgrid/sendgrid` version 4 API
to the new version 7 API. There's been a lot of changes between v4 and v7, so
it will likely require a fairly significant rewrite.

## Official URLs

This library can be found in two places:

 * [Github](https://github.com/supernovus/lum.mailer.php)
 * [Packageist](https://packagist.org/packages/lum/lum-mailer)

## Author

Timothy Totten

## License

[MIT](https://spdx.org/licenses/MIT.html)
