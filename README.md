# Slim Application Template

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/D4rkMindz/gracili/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/D4rkMindz/gracili/?branch=master)
[![Build Status](https://travis-ci.org/D4rkMindz/gracili.svg?branch=master)](https://travis-ci.org/D4rkMindz/gracili)
[![StyleCI](https://styleci.io/repos/127299959/shield?branch=master)](https://styleci.io/repos/127299959)
[![Code Coverage](https://scrutinizer-ci.com/g/D4rkMindz/gracili/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/D4rkMindz/gracili/?branch=master)

----

An application MVC template with Slim and CakePHP QueryBuilder.

`config/` configuration files
`public/` web server files (with indexAction.php and .htaccess)
`templates/` template files
`resources/` other resource files
`src/` PHP source code (The App namespace)
`tests/` test code
`temp/` - temporary files (logfiles, cache)

Run composer install to setup the Project. Afterwards you have to rename the `config/env.example.php` file to 
`config/env.php` and  fill in your data.

You also have to create a database named like the $config['dbconfig']['database'] value in `config/config.php`. You can 
rename this value to any name you like. I recommend to create a table named `users` with the attributes 
`username (VARCHAR 255)`,`first_name (VARCHAR 255)` and `last_name (VARCHAR 255)`.

Afterwards you can start your local Apache Server with [XAMPP](https://www.apachefriends.org/index.html).
To visit your Website you have to open http://localhost/<project_directory>/.