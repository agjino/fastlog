# PHP Fastlog

Fastlog is a sample proof-of-concept web service built in raw PHP, able to handle hundreds of requests
per second, with full security and sanitization checks.

## Getting Started

Fastlog is an implementation of various OOP and general development concepts.
> - Proper *data validation*. While validation and sanitization are regarded as tedious, repetitive tasks, they are the first line of defense and the most important.
> - *Multiple configuration environments* that you can switch to when you're developing, testing and deploying.
> - *Interfaces and Polymorphism* as means of building pluggable modules.
> - *Prepared statements* in MySQL to avoid sql injections.
> - *Caching of results* for compute-intesive calculations to manage acceptable response times
> - *Concurrency handling* in MySQL through Select For Update
> - *Stress testing*
> - *Unit testing*
> - Meaningful *error responses*

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

**To run the built-in unit tests, either make sure the environment data in $config['test_local'] is valid (ex. the specified database exists and the user has permissions) or edit $env in test/bootstrap.php**

### Running

The API calls are on $baseUrl/api/[service]
For example, if $config['appUrl'] is http://localhost/fastlog, some API calls would be:

Create an empty database:
```
http://localhost/fastlog/api/seeder?method=migrate
```
Test http performance by asking the service to add 1000 random http requests:
```
http://localhost/fastlog/api/seeder?method=http&count=1000
```
Send 1000 random write commands within PHP. Much faster than the http method, useful also for testing database performance independent of operating system or network performance:
```
http://localhost/fastlog/api/seeder?method=internal&count=1000
```
Ordinary write request:
```
http://localhost/fastlog/api/logger
POST: {"country":"US","event":"clicks"}
```
Write request with date specified. allowCustomDate must be set to true in config:
```
http://localhost/fastlog/api/logger
POST: {"country":"US","event":"clicks","date":"2017-04-03"}
```
## Authors

* **Albi Gjino**

