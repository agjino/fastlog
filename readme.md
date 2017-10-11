# PHP Fastlog

Fastlog is a sample proof-of-concept web service built in raw PHP, able to handle hundreds of requests
per second, with full security and sanitization checks.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

```
Apache 2.2+
modrewrite for Apache
PHP 7.0+
cURL for PHP, for HTTP seeding and performance tests
PHPUnit 6 to run the unit tests
```

### Installing

Copy or clone the files on you local machine or web server.

Edit config.php with the appropriate values for database access and the base url of the application.
config.php can contain configurations for multiple environments, ex. production or testing. To apply the appropriate
environment, set the $env variable at the top of config.php.

The API calls are on $baseUrl/api/[service]
For example, if $config['appUrl'] is http://localhost/fastlog, some API calls would be:

Create an empty database:
```
http://localhost/fastlog/api/seeder?method=migrate
```
Test http performance by asking the service to add 1000 random http requests
```
http://localhost/fastlog/api/seeder?method=http&count=1000
```
## Authors

* **Albi Gjino**

