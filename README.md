# Information

This is a solution for the credit card checkout task.

# Tested Environment

*   PHP 7.1.9

# Composer Dependencies

*   created from [Symfony Standard Edition] (v3.3.9)
*   [guzzlehttp/guzzle] (v6.3.0)

# Installation

1.  Clone this repository

1.  Execute Composer install:

    ```console
    $ composer install
    ```

    (Keep the default configuration, no database needed.)

1.  Launch built-in web server:

    ```console
    $ php bin/console server:run
    ```

# Usage

Go to the checkout page: <http://localhost:8000/checkout>

# Configuration

You can change the endpoint for the API gateway in the Symfony parameters (located at `app/config/parameters.yml`).

The payment API endpoint URL for the web application (not the tests) is the value for the `production` key of the
parameter `remote_api.payment.urls`.

# Tests

**Test coverage of my own code is 100%.**

You can run the tests by using the provided `phpunit.xml` file.

*   Run all tests:

    ```console
    $ vendor/bin/phpunit
    ```

[Symfony Standard Edition]: https://github.com/symfony/symfony-standard
[guzzlehttp/guzzle]: https://packagist.org/packages/guzzlehttp/guzzle
