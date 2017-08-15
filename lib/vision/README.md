# Google Vision PHP Sample Application

## Description

This simple command-line application demonstrates how to invoke Google 
Vision API from PHP.

## Build and Run
1.  **Enable APIs** - [Enable the Vision API](https://console.cloud.google.com/flows/enableapi?apiid=vision.googleapis.com)
    and create a new project or select an existing project.
2.  **Download The Credentials** - Click "Go to credentials" after enabling the APIs. Click "New Credentials"
    and select "Service Account Key". Create a new service account, use the JSON key type, and
    select "Create". Once downloaded, set the environment variable `GOOGLE_APPLICATION_CREDENTIALS`
    to the path of the JSON key that was downloaded.
3.  **Clone the repo** and cd into this directory
```
    $ git clone https://github.com/GoogleCloudPlatform/php-docs-samples
    $ cd php-docs-samples/vision/api
```
4.  **Install dependencies** via [Composer](http://getcomposer.org/doc/00-intro.md).
    Run `php composer.phar install` (if composer is installed locally) or `composer install`
    (if composer is installed globally).
5.  Run `php vision.php`. The following commands are available:
```
  face         Detect faces in an image using Google Cloud Vision API
  help         Displays help for a command
  label        Detect labels in an image using Google Cloud Vision API
  landmark     Detect landmarks in an image using Google Cloud Vision API
  list         Lists commands
  logo         Detect logos in an image using Google Cloud Vision API
  property     Detect image proerties in an image using Google Cloud Vision API
  safe-search  Detect adult content in an image using Google Cloud Vision API
  text         Detect text in an image using Google Cloud Vision API
```
6. Run `php vision.php COMMAND --help` to print information about the usage of each command.

## Contributing changes

* See [CONTRIBUTING.md](../../CONTRIBUTING.md)

## Licensing

* See [LICENSE](../../LICENSE)
