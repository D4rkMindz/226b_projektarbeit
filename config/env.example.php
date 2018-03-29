<?php
$config['displayErrorDetails'] = true;

$env['db_test']['host'] = '';
$env['db_test']['port'] = '';
$env['db_test']['username'] = '';
$env['db_test']['password'] = '';

$env['db_test']['host'] = '127.0.0.1';
$env['db_test']['port'] = '3306';
$env['db_test']['username'] = 'root';
$env['db_test']['password'] = '';

$env['mailgun']['from'] = '';
$env['mailgun']['apikey'] = '';
$env['mailgun']['domain'] = '';

$env['twig']['assetCache'] ['minify'] = false;
$env['twig']['assetCache'] ['cache_enabled'] = false;
$env['twig']['autoReload'] = true;

return $env;