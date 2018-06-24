<?php

/**
 * Handling email
 *
 * This function is shortening for filter_var.
 *
 * @see filter_var()
 *
 * @param string $email to check
 *
 * @return mixed
 */
function is_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function baseurl($path = '')
{
    $environment = container()->get('environment');
    $scriptName = $environment->get('SCRIPT_NAME');
    $baseUri = dirname(dirname($scriptName));
    $result = str_replace('\\', '/', $baseUri) . $path;
    $result = str_replace('//', '/', $result);
    return $result;
}