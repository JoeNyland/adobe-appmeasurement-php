# adobe-appmeasurement-php

The Adobe AppMeasurement library for PHP lets you use PHP to monitor and evaluate visitor activity on your Web site, application, or service. Once captured, data is available for analysis by the Omniture Suite of products.

### Key Benefits

* Streamlined Implementation for Non-JavaScript Tagging: Management Library provides an alternative to hard-coding Omniture image requests on your Web pages, using familiar syntax and tools (for example, track and tracklink).
* Integrated Data Tracking: Measurement Library lets you incorporate external data into your data collection (external applications, off-line data, etc.)
* Tagless Implementation: Measurement Library supports server-to-server (Web server to Omniture collection server) data transfers that can greatly simplify implementation and maintenance of application and Web site tracking. Server-side measurement also provides a global implementation option independent of the front-end application (mobile application, Web site, off-line application, etc.)
* Visitor ID Management: Measurement Library provides additional visitor identification options that improve visitor tracking, including limiting third-party cookie usage, using the carrier’s subscriber ID, and using server-side tracking of visitor ID.

*Note: This repo is a fork from the official source, available from [here][source-download].*

## Requirements

ToDo: Revise this section
* Supports PHP 5 or later.
* Requires the cURL library installed to use direct server-to-server requests.
* Assumes that all strings are in UTF-8 format. You must convert any non-UTF-8 strings before using them in Measurement Library object (for example, using `iconv()`).

## Installation

### [Composer][composer]

* Add the following to your `composer.json` file:
```
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/JoeNyland/adobe-appmeasurement-php.git"
        }
    ],
    "require": {
        "adobe/appmeasurement-php": "dev-master#1.2.2"
    }
}
```
* Run `composer install`

## Usage
```
<?php

require __DIR__ . '/path/to/vendor/autoload.php';

$client = new \Adobe\AppMeasurement();

$client->sendFromServer = true;
$client->ssl = true;

$client->pageName = 'Home Page'
$client->eVar1 = 'foobar';

$client->track();
```

## Documentation
Please consult the [official Adobe documentation][adobe-documentation-pdf].

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/JoeNyland/adobe-appmeasurement-php. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant](http://contributor-covenant.org) code of conduct.

[source-download]: https://marketing.adobe.com/developer/download/file/L2RldmVsb3Blci91cGxvYWRzL2dhbGxlcnlfY29kZS8wNTdlMTE2NGU4YWQ2ODE3MDRhOGJkZTMzZWNmYWU0OGNmYzNiNTM5LnppcA%3D%3D/UEhQTWVhc3VyZW1lbnQuemlw
[composer]: https://getcomposer.org/
[adobe-documentation-pdf]: https://marketing.adobe.com/resources/help/fr_FR/sc/appmeasurement/php/oms_sc_appmeasure_php.pdf
