# 226B Projektarbeit

----

## What is this project?

This is a Projekt for our school. This battleship web game is coded in PHP and JavaScript mainly. We also use the PHP SLIM Framework since this is the one we prefere. Also used is Twig.

## Installation

Fork or Download the latest Release from GitHub to your locale machine, open the command line in the document root and run the composer installation command.

```bash
$ composer install
```

There are usually many environments that your application is installed (you should not develop on the productive server ;) ). There is a environment configuration file to solve the problem with different configurations and password per environment.
You have to rename the `config/env.example.php` file to `config/env.php` and  fill in your data.
If the `config/env.php` file is not found, the application will look for it in the parent directory (most probably the htdocs folder in your xampp environment). If the `env.php` file is not found there, the application will throw an Error.
You can also define a `APP_ENV` environment variable and add a `<APP_ENV>.php` file in the `config/` folder to specify public configurations for e.g. your [CI Service](https://en.wikipedia.org/wiki/Continuous_integration) (NOT PASSWORD OR ANY SECRETS)

Make shure to NEVER deploy secret data like passwords to GitHub or any other versioning tool.

Afterwards you can start your local Apache Server with [XAMPP](https://www.apachefriends.org/index.html).
To visit your Website you have to open http://localhost/<project_directory>/.

## Structure

The Application is build on the [MVC Design Pattern](https://de.wikipedia.org/wiki/Model_View_Controller). All source code should be in the `src/` directory.

| Folder            | Content                                                                                                                                |
| ----------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `config/`         | All files required for the configuration of your application                                                                           |
| `public/`         | Static accessible web files (images, css, fonts, etc.)                                                                                 |
| `resources/`      | Other resources, that should not be public (migrations or translations)                                                                |
| `src/`            | All PHP source code (The App namespace)                                                                                                |
| `src/Controller/` | Directory for all Controllers (one per 'page')                                                                                         |
| `src/Datarow/`    | Classes for the Database sets that are returend and hydrated (one per 'select')                                                        |
| `src/Service/`    | All services for the businesslogic are here. It is recommended to structure it [modular](https://en.wikipedia.org/wiki/Modular_design) |
| `src/Table/`      | The classes for the database queries (one per 'table')                                                                                 |
| `src/Util/`       | Helper classes like a validation context.                                                                                              |
| `templates/`      | The [Twig](https://twig.symfony.com/) template files                                                                                   |
| `tests/`          | Directory for tests                                                                                                                    |
| `temp/`           | Temporary files (logfiles, cache)                                                                                                      |


## Running the tests

Supposing, that you have [Apache Ant](https://ant.apache.org/) installed, just run the following command to run all PHPUnit tests

```bash
$ ant phpunit
```

### Running the tests with coverage

Run the following command to generate a coverage file in the generated build folder `build/logs/clover.xml`

```bash
$ ant phpunit-coverage
```

### Running single test files

To run just a few tests, not the whole test suite, you can run the following command after you added the `@group actual` annotation to the test class docblock

```bash
$ ant phpunit-actual
```

The test class doc block to run the test in the actual group must look like this

```php
/**
 * My test class
 * @group actual
 */
class MyTest
{
  // PHPUnit Test here ...
}
```

## Built with

  * [Slim PHP](https://slimframework.com) - The Basic framework
  * [Composer](https://getcomposer.org) - The dependency manager
  * [PHPUnit](https://phpunit.de/) - Test suite runner
  * [Twig Template Engine](https://twig.symfony.com/) - The templating engine
  * [CakePHP Query Builder](https://book.cakephp.org/3.0/en/orm/query-builder.html) - To query the database
  * [Apache Ant](https://ant.apache.org) - Build script executor

## License

Copyright 2018

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## TODO
- Testfälle nachliefern
- 3-Schichten Architektur besser erklären